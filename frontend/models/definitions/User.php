<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"username", "password", "phone"})
 *
 * @SWG\Property(property="username", type="string")
 * @SWG\Property(property="password", type="string")
 * @SWG\Property(property="phone", type="string")
 * @SWG\Property(property="gender", type="integer")
 * @SWG\Property(property="first_name", type="string")
 * @SWG\Property(property="last_name", type="string")
 * @SWG\Property(property="birthday", type="string")
 */
class User
{
}