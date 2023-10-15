<?php

namespace WeirdoPanel\Support\Contract;

use WeirdoPanel\Support\User\UserProvider;

/**
 * @method static array makeAdmin(int $id, bool $is_superuser = false)
 * @method static mixed findUser(int $id)
 * @method static mixed findUserForEmail(string $email)
 * @method static void deleteAdmin(int $id)
 * @method static array getAdmins()
 * @method static \Illuminate\Database\Eloquent\Model getUserModelInstance()
 * @method static \Illuminate\Database\Eloquent\Model getPersonalAccessTokenInstance()
 * @see UserProvider
 */
class UserProviderFacade extends BaseFacade
{

}
