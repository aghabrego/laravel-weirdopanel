<?php

namespace App\Http\Livewire\Admin\Personalaccesstoken;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class Create extends Component
{
    use WithFileUploads;

    public $name;
    public $models;
    public $dropdown;
    public $model;
    public $selection;
    public $token;

    protected $rules = [
        'model' => 'required',
        'name' => 'required|min:2',
    ];

    public function updated($input)
    {
        $this->validateOnly($input);
    }

    public function closeModal()
    {
        $this->hideDropdown();
    }

    private function getModels($query = null)
    {
        $userModel = UserProviderFacade::getUserModelInstance();
        $data = $userModel->query();
        $user = auth()->user();
        if (!$user->hasPermission('fullAccess')) {
            $data->where('user_id', $user->getKey())
                ->orWhere('id', $user->getKey());
        }
        if ($this->model) {
            $array = $this->searchable();
            $data->where(function (Builder $query) use ($array){
                foreach ($array as $item) {
                    if(!Str::contains($item, '.')) {
                        $query->orWhere($item, 'like', '%' . $this->model . '%');
                    } else {
                        $array = explode('.', $item);
                        $query->orWhereHas($array[0], function (Builder $query) use ($array) {
                            $query->where($array[1], 'like', '%' . $this->model . '%');
                        });
                    }
                }
            });
        }
        $data->latest('id');
        $data->limit(100);
        $result = $data->get()->pluck('name', 'id');

        return $result;
    }

    public function searchable()
    {
        return ['name', 'email'];
    }

    public function setModel()
    {
        $this->models = $this->getModels();
        $this->showDropdown();
    }

    public function setSuggestedModel($key)
    {
        $this->selection = ['id' => $key, 'name' => $this->models[$key]];
        $this->model = $this->models[$key];
        $this->hideDropdown();
    }

    public function updatedModel($value)
    {
        $value = $value == '' ? null : $value;
        $this->models = $this->getModels($value);
        $this->showDropdown();
    }

    public function hideDropdown()
    {
        $this->dropdown = false;
    }

    public function showDropdown()
    {
        $this->dropdown = true;
    }

    public function create()
    {
        if($this->getRules())
            $this->validate();

        if(!isset($this->selection, $this->selection['id'])) {
            $this->addError('route', __('Selection is invalid'));

            return;
        }

        $userModel = UserProviderFacade::getUserModelInstance();
        $user = $userModel->query()->findOrFail($this->selection['id']);
        $organization = $user->organization ?? null;
        $remoteLicense = Arr::get(config('weirdo_panel.custom_remote_license_verification'), 'url_base');
        $expiredAt = null;
        if (!empty($organization) && !empty($remoteLicense)) {
            $client = new \GuzzleHttp\Client(['base_uri' => $remoteLicense]);
            $response = $client->request('GET', "/app/licenses/{$organization->ruc}/1");
            if ($response->getStatusCode() !== 200) {
                $this->addError('route', __('Failed to verify remote license'));

                return;
            }

            $body = $response->getBody();
            /** @var array $content*/
            $content = json_decode($body->getContents(), true);
            /** @var string $keyValue */
            $keyValue = Arr::get(config('weirdo_panel.custom_remote_license_verification'), 'key_value');
            if (empty($keyValue)) {
                $this->addError('route', __('Failed to verify remote license'));

                return;
            }
            $expiredAt = Arr::get($content, $keyValue, null);
        }
        if (!empty($expiredAt)) {
            $expiredAt = new Carbon($expiredAt);
        }

        $token = $user->createToken($this->name, ['*'], $expiredAt);

        $this->dispatchBrowserEvent('show-message', ['type' => 'success', 'message' => __('CreatedMessage', ['name' => __('Personalaccesstoken') ])]);
        $this->dispatchBrowserEvent('name-modal-personal-access-token-is-open', ['modalTokenIsOpen' => true, 'token' => $this->token]);
        $this->reset();

        $this->token = $token->plainTextToken;
    }

    public function render()
    {
        return view('livewire.admin.personalaccesstoken.create')
            ->layout('admin::layouts.app', ['title' => __('CreateTitle', ['name' => __('Personalaccesstoken') ])]);
    }
}
