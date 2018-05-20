<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function create(){
        //検索キーワードを取得
        $keyword = request()->keyword;
        $items = [];
        if($keyword){
            //クライアントを作成
            $client = new \RakutenRws_Client();
            //アプリIDを設定
            $client->setApplicationId(env('RAKUTEN_APPLICATION_ID'));
            
            $rws_response = $client->execute('IchibaItemSearch', [
                'keyword' => $keyword,
                //画像があるもののみに絞り込み
                'imageFlag' => 1,
                //20件検索
                'hits' => 20,
            ]);
            
            // 扱い易いように Item としてインスタンスを作成する（保存はしない）
            foreach($rws_response->getData()['Items'] as $rws_item){
                $item = new Item();
                $item->code = $rws_item['Item']['itemCode'];
                $item->name = $rws_item['Item']['itemName'];
                $item->url = $rws_item['Item']['itemUrl'];
                //画像 URL 末尾に含まれる ?_ex=128x128 を削除
                $item->image_url = str_replace('?_ex=128x128', '', $rws_item['Item']['mediumImageUrls'][0]['imageUrl']);
                $items[] = $item;
            }
        }
        
        return view('items.create', [
            'keyword' => $keyword,
            'items' => $items,
            ]);
    }
}
