<?php

use Mockery as m;
use Mpociot\Firebase\Tests\Fixtures\User;

class FirebaseSyncTest extends Orchestra\Testbench\TestCase
{

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');

        \Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function test_sync_requires_valid_settings()
    {
        $this->setExpectedException(ErrorException::class, 'You must provide a baseURI variable.');

        $user = new User();
        $user->name = 'Foo';
        $user->save();
    }

    public function test_creates_model_in_firebase()
    {
        $this->app['config']->set('services.firebase.database_url', 'https://firebase.foo');

        $firebaseStub = m::mock('\\Firebase\\FirebaseStub[set]');
        $firebaseStub->shouldReceive('set')
            ->once()
            ->with('users/1', m::type('array'));

        $user = new User();
        $user->name = 'Foo';
        $user->setFirebaseClient($firebaseStub);
        $user->save();

        $this->assertDatabaseHas('users', [
            'name' => 'Foo'
        ]);
    }

    public function test_updates_model_in_firebase()
    {
        $this->app['config']->set('services.firebase.database_url', 'https://firebase.foo');

        $firebaseStub = m::mock('\\Firebase\\FirebaseStub[set, update]');
        $firebaseStub->shouldReceive('set')
            ->once()
            ->with('users/1', m::type('array'));

        $firebaseStub->shouldReceive('update')
            ->once()
            ->with('users/1', m::type('array'));

        $user = new User();
        $user->name = 'Foo';
        $user->setFirebaseClient($firebaseStub);
        $user->save();

        $user = User::find(1);
        $user->setFirebaseClient($firebaseStub);
        $user->name = 'Bar';
        $user->save();

        $this->assertDatabaseHas('users', [
            'name' => 'Bar'
        ]);
    }

    public function test_deletes_model_in_firebase()
    {
        $this->app['config']->set('services.firebase.database_url', 'https://firebase.foo');

        $firebaseStub = m::mock('\\Firebase\\FirebaseStub[set, delete]');
        $firebaseStub->shouldReceive('set')
            ->once()
            ->with('users/1', m::type('array'));

        $firebaseStub->shouldReceive('delete')
            ->once()
            ->with('users/1');

        $user = new User();
        $user->name = 'Foo';
        $user->setFirebaseClient($firebaseStub);
        $user->save();

        $user = User::find(1);
        $user->setFirebaseClient($firebaseStub);
        $user->delete();

        $this->assertDatabaseMissing('users', [
            'name' => 'Foo'
        ]);
    }
}
