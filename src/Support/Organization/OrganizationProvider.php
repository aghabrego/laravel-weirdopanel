<?php

namespace WeirdoPanel\Support\Organization;

class OrganizationProvider
{
    public function getOrganizationModel()
    {
        return config('weirdo_panel.organization_model');
    }

    public function getOrganizationModelInstance()
    {
        return app(OrganizationProvider::getOrganizationModel());
    }
}
