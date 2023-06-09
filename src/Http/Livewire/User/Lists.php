<?php

namespace WeirdoPanel\Http\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class Lists extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search;

    protected $queryString = ['search'];

    protected $listeners = ['userDeleted'];

    public $sortType;
    public $sortColumn;

    public function userDeleted()
    {
        // Nothing ..
    }

    public function searchable()
    {
        return ['name', 'email'];
    }

    public function sort($column)
    {
        $sort = $this->sortType == 'desc' ? 'asc' : 'desc';

        $this->sortColumn = $column;
        $this->sortType = $sort;
    }

    public function render()
    {
        $userModel = UserProviderFacade::getUserModelInstance();
        $data = $userModel->query();

        $user = auth()->user();
        if (!$user->hasPermission('fullAccess')) {
            $data->where('user_id', $user->getKey())
                ->orWhere('id', $user->getKey());
        }

        if ($this->search) {
            $array = $this->searchable();
            $data->where(function (Builder $query) use ($array){
                foreach ($array as $item) {
                    if(!\Str::contains($item, '.')) {
                        $query->orWhere($item, 'like', '%' . $this->search . '%');
                    } else {
                        $array = explode('.', $item);
                        $query->orWhereHas($array[0], function (Builder $query) use ($array) {
                            $query->where($array[1], 'like', '%' . $this->search . '%');
                        });
                    }
                }
            });
        }

        if($this->sortColumn) {
            $data->orderBy($this->sortColumn, $this->sortType);
        } else {
            $data->latest('id');
        }

        $data = $data->paginate(config('weirdo_panel.pagination_count', 15));

        return view('admin::livewire.user.lists', [
            'users' => $data
        ])->layout('admin::layouts.app', ['title' => __('ListTitle', ['name' => __('Users')])]);
    }
}
