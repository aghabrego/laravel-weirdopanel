<?php

namespace WeirdoPanel\Support\Auth;

use WeirdoPanel\Support\Contract\UserProviderFacade;

class AdminIdentifier
{

    public function check($userId)
    {
        $user = UserProviderFacade::findUser($userId);

        return $user->panelAdmin()->exists();
    }

}
