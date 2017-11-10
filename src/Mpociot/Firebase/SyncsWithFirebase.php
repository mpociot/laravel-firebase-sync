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
            try {
                $model->saveToFirebase('set');
            } catch (\Exception $e) {
                \Log::critical($e);
            }
        });
        static::updated(function ($model) {
            try {
                $model->saveToFirebase('update');
            } catch (\Exception $e) {
                \Log::critical($e);
            }
        });
        static::deleted(function ($model) {
            try {
                $model->saveToFirebase('delete');
            } catch (\Exception $e) {
                \Log::critical($e);
            }
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
        $path = $this->getTable() . '/' . $this->getKey();

        if ($mode === 'set') {
            $this->firebaseClient->set($path, $this->getFirebaseSyncData());
        } elseif ($mode === 'update') {
            $this->firebaseClient->update($path, $this->getFirebaseSyncData());
        } elseif ($mode === 'delete') {
            $this->firebaseClient->delete($path);
        }
    }
}
