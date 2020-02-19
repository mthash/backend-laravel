<?php

namespace App\Models\User;

use App\Models\User\User;
use App\Models\User\Asset as UserAsset;

class AssetRepository
{
    public static function find (User $user,  \App\Models\Asset\Asset $asset) : UserAsset
    {
        $record = Asset::where([
            ['status', '>', '0'],
            ['user_id', '=', $user->id],
            ['asset_id', '=', $asset->id]
        ])->first();

        if (!$record)
        {
            $record = new UserAsset();
            $record->asset_id   = $asset->id;
            $record->user_id    = $user->id;
            $record->is_visible = 1;
            $record->save();
        }

        return $record;
    }

    /**
     * @param User $user
     * @return \MtHash\Model\AbstractModel[]|Asset[]|\Phalcon\Mvc\Model\ResultsetInterface|void
     */
//    public static function allVisible (User $user)
//    {
////        return Asset::find (
////            [
////                'status > 0 and user_id = ?0 and is_visible = 1', 'bind' => [$user->id]
////            ]
////        );
//        return Asset::where(
//            [
//                ['status', '>', 0],
//                ['user_id', $user->id],
//                ['is_visible', 1]
//            ]
//        )->get();
//    }

    static public function allVisible (User $user)
    {
        return UserAsset::where([
            ['status', '>', '0'],
            ['user_id', '=', $user->id],
            ['is_visible', '=', '1']
        ])->get();
    }
}
