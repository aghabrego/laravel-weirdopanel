<?php

namespace WeirdoPanel\Support\Organization;

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
     * @return string
     */
    public function getOrganizationId()
    {
        $user = UserProviderFacade::findUser(auth()->id());
        $withOrg = (method_exists($user, config('weirdo_panel.user_set_organization.get')));

        return $withOrg ? $user->{config('weirdo_panel.user_set_organization.get')}() : '';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getOrganizations()
    {
        $this->setDefaultConnection();
        $user = UserProviderFacade::findUser(auth()->id());
        if (method_exists($user, config('weirdo_panel.user_organization_relationship'))) {
            $organizations = $user->{config('weirdo_panel.user_organization_relationship')};

            return $organizations->map(function ($val) {
                return (object)['id' => $val->id, 'name' => $val->pretty_name];
            });
        }

        return collect([]);
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
        if (method_exists($user, config('weirdo_panel.user_organization_relationship'))) {
            $user->{config('weirdo_panel.user_organization_relationship')}()->sync($organizations);

            return [
                'type' => 'success',
                'message' => "Usuario '$id' se asignó a la organización",
            ];
        }

        return [];
    }
}
