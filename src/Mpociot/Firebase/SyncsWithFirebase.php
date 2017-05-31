<?php

namespace Mpociot\Firebase;

use Firebase\FirebaseInterface;
use Firebase\FirebaseLib;

/**
 * Class SyncsWithFirebase
 * @package App\Traits
 */
trait SyncsWithFirebase
{

    /**
     * @var FirebaseInterface|null
     */
    protected $firebaseClient;

    /**
     * Boot the trait and add the model events to synchronize with firebase
     */
    public static function bootSyncsWithFirebase()
    {
        static::created(function ($model) {
            $model->saveToFirebase('set');
        });
        static::updated(function ($model) {
            $model->saveToFirebase('update');
        });
        static::deleted(function ($model) {
            $model->saveToFirebase('delete');
        });
        static::restored(function ($model) {
            $model->saveToFirebase('set');
        });
    }

    /**
     * @param FirebaseInterface|null $firebaseClient
     */
    public function setFirebaseClient($firebaseClient)
    {
        $this->firebaseClient = $firebaseClient;
    }

    /**
     * @return array
     */
    protected function getFirebaseSyncData()
    {
        if ($fresh = $this->fresh()) {
            return $fresh->toArray();
        }
        return [];
    }

    /**
     * @param $mode
     */
    protected function saveToFirebase($mode)
    {
        if (is_null($this->firebaseClient)) {
            $this->firebaseClient = new FirebaseLib(config('services.firebase.database_url'), config('services.firebase.secret'));
        }
        $path = $this->getTable() . '/item' . $this->getKey();

        if ($mode === 'set') {
            $this->firebaseClient->set($path, $this->getFirebaseSyncData());
        } elseif ($mode === 'update') {
            $this->firebaseClient->update($path, $this->getFirebaseSyncData());
        } elseif ($mode === 'delete') {
            $this->firebaseClient->delete($path);
        }
    }
}
