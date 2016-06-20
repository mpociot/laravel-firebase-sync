<?php

namespace Mpociot\Firebase\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class User extends Model
{

    use SyncsWithFirebase;
}
