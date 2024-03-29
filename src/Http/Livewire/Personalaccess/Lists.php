<?php

namespace WeirdoPanel\Http\Livewire\Personalaccess;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class Lists extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search;

    protected $queryString = ['search'];

    protected $listeners = ['personalaccesstokenDeleted'];

    public $sortType;
    public $sortColumn;

    public function personalaccesstokenDeleted()
    {
        // Nothing ..
    }

    public function sort($column)
    {
        $sort = $this->sortType == 'desc' ? 'asc' : 'desc';

        $this->sortColumn = $column;
        $this->sortType = $sort;
    }

    public function searchable()
    {
        return ['name'];
    }

    public function render()
    {
        $model = UserProviderFacade::getPersonalAccessTokenInstance();
        $data = $model->query();

        $array = $this->searchable();
        $data->where(function (Builder $query) use ($array){
            foreach ($array as $item) {
                if(!Str::contains($item, '.')) {
                    $query->orWhere($item, 'like', '%' . $this->search . '%');
                } else {
                    $array = explode('.', $item);
                    $query->orWhereHas($array[0], function (Builder $query) use ($array) {
                        $query->where($array[1], 'like', '%' . $this->search . '%');
                    });
                }
            }
        });

        if($this->sortColumn) {
            $data->orderBy($this->sortColumn, $this->sortType);
        } else {
            $data->latest('id');
        }

        $data = $data->paginate(config('weirdo_panel.pagination_count', 15));

        return view('admin::livewire.personalaccess.lists', [
            'personalaccesstokens' => $data
        ])->layout('admin::layouts.app', ['title' => __(Str::plural('Personalaccesstoken')) ]);
    }
}
