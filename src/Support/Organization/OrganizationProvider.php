<?php

namespace WeirdoPanel\Support\Organization;

use Illuminate\Support\Facades\Cache;
use WeirdoPanel\Traits\CustomConnection;
use WeirdoPanel\Support\Contract\OrganizationFacade;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class OrganizationProvider
{
    use CustomConnection;
    
    /**
     * @return string
     */
    public function getOrganizationModel()
    {
        return config('weirdo_panel.organization_model');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getOrganizationModelInstance()
    {
        return app(OrganizationProvider::getOrganizationModel());
    }

    /**
     * @param string $org
     * @return string
     */
    public function getORG()
    {
        $user = UserProviderFacade::findUser(auth()->id());
        $primaryKey = $user->primaryKey;
        $key = $user->getKey();
        $fullKey = "weirdopanel_org_{$primaryKey}_{$key}";
        $orgId = Cache::get($fullKey);
        if (!empty($orgId)) {
            return $orgId;
        }
        if (!isset($user->{config('weirdo_panel.user_organization')})) {
            return '';
        }

        return (string)$user->{config('weirdo_panel.user_organization')};
    }

    /**
     * @return int
     */
    public function getOrganizationId(): int
    {
        $orgId = $this->getORG();

        return $orgId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getOrganizations()
    {
        $this->setDefaultConnection();
        $organizationModel = OrganizationFacade::getOrganizationModelInstance();
        $userId = auth()->id();
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $organizationModel::query()->orderBy('id', 'desc');
        $query->where('user_id', $userId);
        $organizations = $query->get()->map(function ($val) {
            return (object)['id' => $val->id, 'name' => $val->pretty_name];
        });

        return $organizations;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function allOrganizations()
    {
        $this->setDefaultConnection();
        $organizationModel = OrganizationFacade::getOrganizationModelInstance();
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $organizationModel::query()->orderBy('id', 'desc');
        $organizations = $query->get()->map(function ($val) {
            return (object)['id' => $val->id, 'name' => $val->pretty_name];
        });

        return $organizations;
    }

    /**
     * @return array
     */
    public static function organizations()
    {
        /** @var \Illuminate\Support\Collection $organizationCollection */
        $organizationCollection = OrganizationFacade::getOrganizations();
        $data = $organizationCollection->pluck('name', 'id')->prepend('--SELECCIONAR--')->toArray();

        return $data;
    }

    /**
     * @param int $id
     * @param array $organizations
     * @return array
     */
    public function makeOrganization($id, $organizations = [])
    {
        $this->setDefaultConnection();
        $user = UserProviderFacade::findUser($id);
        $user->organizations()->sync($organizations);

        return [
            'type' => 'success',
            'message' => "Usuario '$id' se asignó a la organización",
        ];
    }
}
