<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Chanpion;
use App\Skill;
use App\Roll;
use App\Tag;
use App\TagBox;


use Illuminate\Support\Facades\Auth;

// use Illuminate\Auth\Events\Registered;

class ChanpionsController extends Controller
{
// ---------------------------------
// チャンピオン系
// ---------------------------------
    public function indexChanpion(){
        $chanpionsData = Chanpion::all();
        return view('chanpions.index', compact('chanpionsData'));
    }

    public function newChanpion() {
        //chanpion登録画面を呼ぶ
        //ロールカテゴリーデータをDBから呼び出す
        //ユーザーデータをDBから呼び出す
        $rollCategorys = Roll::all();
        $userDatas = User::all();
        $tagDatas = Tag::all();

        return view('chanpions.new' ,compact(['rollCategorys','userDatas','tagDatas']));
    }

    public function createChanpion(Request $request) {

        $request->validate([
            'name' => 'string|max:255',
            'sub_name' => 'string|max:255',
            'popular_name' => 'string|max:20',
            'feature' => 'string|max:60',
            'main_roll_id' => 'nullable',
            'sub_roll_id' => 'different:main_roll_id|nullable|',
            'be_cost' => 'nullable|numeric',
            'rp_cost' => 'nullable|numeric',
            'chanpion_img' => 'nullable|file|image|max:10240',
            'st_attack' => 'required|numeric|max:10|min:1',
            'st_magic' => 'required|numeric|max:10|min:1',
            'st_toughness' => 'required|numeric|max:10|min:1',
            'st_mobility' => 'required|numeric|max:10|min:1',
            'st_difficulty' => 'required|numeric|max:10|min:1',
            'user_id' => 'required',
            'chanpion_tagId[]' => 'array|string'
        ]
        ,[
            'name.required' => '名前は必須入力です',
            'name.string' => '文字列で入力してください',
            'name.max' => '名前は255文字以内で入力して下さい',
            'sub_name.required' => '英名は必須入力です',
            'sub_name.max' => '英名は255文字以内で入力して下さい',
            'sub_name.string' => '文字列で入力して下さい',
            'popular_name.string' => '文字列で入力して下さい',
            'popular_name.max' => '20文字以内で入力して下さい',
            'feature.string' => '文字列で入力して下さい',
            'feature.max' => '60文字以内で入力して下さい',
            'sub_roll_id.different' => 'メインロールと同一ロールは、選択できません',
            'be_cost.numeric' => '数値ではありません',
            'rp_cost.numeric' => '数値ではありません',
            'chanpion_img.file' => 'アップロードできませんでした',
            'chanpion_img.image' => 'アップロードできない形式です',
            'chanpion_img.nullable' => '画像は後にいれることが出来ます',
            'st_attack.required' => '必須入力です',
            'st_magic.required' => '必須入力です',
            'st_toughness.required' => '必須入力です',
            'st_mobility.required' => '必須入力です',
            'st_difficulty.required' => '必須入力です',
            'st_attack.max' => '10以下の値を入力してください',
            'st_attack.min' => '0以上の値を入力してください',
            'st_magic.max' => '10以下の値を入力してください',
            'st_magic.min' => '0以上の値を入力してください',
            'st_toughness.max' => '10以下の値を入力してください',
            'st_toughness.min' => '0以上の値を入力してください',
            'st_mobility.max' => '10以下の値を入力してください',
            'st_mobility.min' => '0以上の値を入力してください',
            'st_difficulty.max' => '10以下の値を入力してください',
            'st_difficulty.min' => '0以上の値を入力してください',
            'user_id.required' => '必須入力です'
        ]);
            //一つずつ入れた方が後の変更に対応しやすい
            $chanpionData = new Chanpion;
            $chanpionData->name = $request->name;
            $chanpionData->sub_name = $request->sub_name;
            $chanpionData->popular_name = $request->popular_name;
            $chanpionData->feature = $request->feature;
            $chanpionData->main_roll_id = $request->main_roll_id;
            $chanpionData->sub_roll_id = $request->sub_roll_id;
            $chanpionData->be_cost = $request->be_cost;
            $chanpionData->rp_cost = $request->rp_cost;

            $file = $request->file('chanpion_img');
            $filename = $file->getClientOriginalName();
            $chanpionData->chanpion_img = $request->file('chanpion_img')->storeAs('img.chanpion' , $filename);

            //ファイル・リサイズ
            // var_dump($name);
            // $updateFile = InterventionImage::make($file)->resize(350, null, function ($constraint) {$constraint->aspectRatio();});
            // $chanpionData->chanpion_img = $updateFile->store('/img/chanpion');

            $chanpionData->st_attack = $request->st_attack;
            $chanpionData->st_magic = $request->st_magic;
            $chanpionData->st_toughness = $request->st_toughness;
            $chanpionData->st_mobility = $request->st_mobility;
            $chanpionData->st_difficulty = $request->st_difficulty;
            $chanpionData->user_id = $request->user_id;
            $chanpionData->chanpion_tag = $request->chanpion_tag;
            $chanpionData->save();
            //リダイレクトする、その時にフラッシュメッセージをいれる
            return redirect('/chanpions')->with('flash_message',__('Registered.'));
    }

    public function editChanpion($id){
        // GETパラメータが数字かどうかをチェックする
        //事前にチェックする事で無駄なアクセスを減らせる
        if(!ctype_digit($id)){

            return redirect('/chanpions')->with('flash_message', __('Invalid operation was performed.'));
        }

       $chanpion = Chanpion::find($id);
        // $chanpion = Auth::user()->drills()->find($id);
        return view('chanpions.edit', compact('chanpion'));
    }

    public function updateChanpion(Request $request, $id){
    // GETパラメータが数字かどうかをチェックする
    if(!ctype_digit($id)){
        return redirect('/chanpions')->with('flash_message', __('Invalid operation was performed.'));
    }

    $chanpionsData = Chanpion::find($id);
    $chanpionsData->fill($request->all())->save();

    return redirect('/chanpions')->with('flash_message', __('Updated.'));
}

    public function deleteChanpion($id) {
        if(!ctype_digit($id)){
            return redirect('/chanpions')->with('flash_message', __('Invalid operation was performed.'));
    }
    Chanpion::find($id)->delete();

    return redirect('/chanpions')->with('flash_message', __('Deleted.'));
    }

// ---------------------------------
// スキル系
// ---------------------------------

    public function indexSkill(){
        $skillsData = Skill::all();
        return view('chanpions.skillIndex', compact('skillsData'));
    }

    public function newSkill() {
        //chanpionスキル登録画面を呼ぶ
        return view('chanpions.newSkill');
    }

    public function createSkill(Request $request) {

        $request->validate([
            'name' => 'string|max:255',
            'na_name' => 'string|max:255',
            'skill_type' => 'string',
            'chanpion_id' => 'required|numeric',
            'text' => 'string|nullable|max:255',
            // 'skill_icon_1' => 'nullable|file|image',
            // 'skill_icon_2' => 'nullable|file|image',
        ]
        ,[
            'name.required' => '名前は必須入力です',
            'name.string' => '文字列で入力してください',
            'name.max' => '名前は255文字以内で入力して下さい',
            'na_name.required' => '英名は必須入力です',
            'na_name.max' => '英名は255文字以内で入力して下さい',
            'na_name.string' => '文字列で入力して下さい',
            'skill_type.string' => '文字列で入力して下さい',
            'text.string' => '文字列で入力して下さい',
            'text.max' => 'テキストは255文字以内で入力して下さい',
            'text.nullable' => 'テキストの入力をお忘れではないですか？',
            'skill_icon_1.file' => 'アップロードできませんでした',
            'skill_icon_1.image' => 'アップロードできない形式です',
            'skill_icon_1.nullable' => '画像は後にいれることが出来ます',
            'skill_icon_2.file' => 'アップロードできませんでした',
            'skill_icon_2.image' => 'アップロードできない形式です',
            'skill_icon_2.nullable' => '画像は後にいれることが出来ます',
        ]);
            $skillDatas = new Skill;

            $skillDatas->fill($request->all())->save();

            error_log('ここまで処理しました');


            //リダイレクトする、その時にフラッシュメッセージをいれる
            return redirect('/skills')->with('flash_message',__('Registered.'));
    }

    public function editSkill($id) {
        if(!ctype_digit($id)){
            return redirect('/skills')->with('flash_message',__('Invalid operation was performed.'));
        }

        $skillData = Skill::find($id);

        return view('chanpions.skillEdit', compact('skillData'));
    }

    public function updateSkill(Request $request ,$id) {
        if(!ctype_digit($id)){
            return redirect('/skills')->with('flash_message',__('Invalid operation was performed.'));
        }

        $skillData = Skill::find($id);
        $skillData->fill($request->all())->save();

        return redirect('/skills')->with('flash_message', __('Updated.'));
    }

    public function deleteSkill($id) {
        if(!ctype_digit($id)){
            return redirect('/skills')->with('flash_mesage', __('Invalid operation was performed.'));
        }
        Skill::find($id)->delete();
        return redirect('/skills')->with('flash_message', __('Deleted.'));
    }

// ---------------------------------
// ロール系
// ---------------------------------
    public function indexRoll(){
        $rollsData = Roll::all();
        return view('chanpions.rollIndex', compact('rollsData'));
}

    public function newRoll(){
        return view('chanpions.newRoll');
    }

    public function createRoll(Request $request) {
        $request->validate([
            'name' => 'string|max:20',
        ],
        [
            'name.string' => 'ロール名は文字で入力して下さい',
            'name.max' => 'ロール名は20文字以内で入力して下さい',
        ]);
        error_log('バリデーションOK');

        $rollsData = new Roll;
        $rollsData->fill($request->all())->save();
        return redirect('/rolls')->with('flash_message', __('New Roll Registered.'));
    }

    public function editRoll($id){
        // GETパラメータが数字かどうかをチェックする
        //事前にチェックする事で無駄なアクセスを減らせる
        if(!ctype_digit($id)){
            return redirect('/rolls')->with('flash_message', __('Invalid operation was performed.'));
        }

       $roll = Roll::find($id);
        // $chanpion = Auth::user()->drills()->find($id);
        return view('chanpions.rollEdit', compact('roll'));
    }

    public function updateRoll(Request $request ,$id) {
        if(!ctype_digit($id)){
            return redirect('/rolls')->with('flash_message',__('Invalid operation was performed.'));
        }
        $rollData = Roll::find($id);
        $rollData->fill($request->all())->save();
        return redirect('/rolls')->with('flash_message', __('Updated Roll.'));
    }

    public function deleteRoll($id) {
        if(!ctype_digit($id)){
            return redirect('/rolls')->with('flash_mesage', __('Invalid operation was performed.'));
        }
        Roll::find($id)->delete();
        return redirect('/rolls')->with('flash_message', __('Deleted Roll.'));
    }

// ---------------------------------
// タグ系
// ---------------------------------
public function indexTag(){
    $tagsData = Tag::all();
    return view('chanpions.tagIndex', compact('tagsData'));
}

public function newTag(){
    return view('chanpions.newTag');
}

public function createTag(Request $request) {
    $request->validate([
        'name' => 'string|max:20',
        'sub_name' => 'string|max:20',
    ],
    [
        'name.string' => 'ロール名は文字で入力して下さい',
        'sub_name.string' => 'ロール名は文字で入力して下さい',
        'name.max' => 'ロール名は20文字以内で入力して下さい',
        'sub_name.max' => 'ロール名は20文字以内で入力して下さい',
    ]);
    error_log('バリデーションOK');

    $tagsData = new Tag;
    $tagsData->fill($request->all())->save();
    return redirect('/tags')->with('flash_message', __('New Tag Registered.'));
}

public function editTag($id){
    // GETパラメータが数字かどうかをチェックする
    //事前にチェックする事で無駄なアクセスを減らせる
    if(!ctype_digit($id)){

        return redirect('/tags')->with('flash_message', __('Invalid operation was performed.'));
    }

   $tag = Tag::find($id);
    // $tag = Auth::user()->drills()->find($id);
    return view('chanpions.tagEdit', compact('tag'));
}
public function updateTag(Request $request ,$id) {
    if(!ctype_digit($id)){
        return redirect('/tags')->with('flash_message',__('Invalid operation was performed.'));
    }

    $tagData = Tag::find($id);
    $tagData->fill($request->all())->save();

    return redirect('/tags')->with('flash_message', __('Updated Tag.'));
}
public function deleteTag($id) {
    if(!ctype_digit($id)){
        return redirect('/tags')->with('flash_mesage', __('Invalid operation was performed.'));
    }
    Tag::find($id)->delete();
    return redirect('/tags')->with('flash_message', __('Deleted Tag.'));
}
// ---------------------------------
// タグボックス系
// ---------------------------------

            public function newTagBox($id){
                if(!ctype_digit($id)){
                    return redirect('/chanpions')->with('flash_mesage', __('Invalid operation was performed.'));
                }
                $chanpionData = Chanpion::find($id);
                $tagDatas  = Tag::all();
                return view('chanpions.newTagBox',compact(['chanpionData','tagDatas']));
            }

            public function createTagBox(Request $request) {
              $request->validate([
                // 'name' => 'string|max:255',
                // 'chanpion_tag_id_1' => 'required|string|max:20'
              ]);
                $tagboxDatas = new TagBox;
                $tagboxDatas->fill($request->all())->save();
                return redirect('/chanpions')->with('flash_message', __('New TagBox Registered.'));
            }


}