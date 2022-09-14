<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Faerie;
use App\Services\Profanity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function createComment(Request $request, $slug){
        $faerie = Faerie::where('uuid', $slug)->firstOrFail();
        $message = Profanity::hasProfanity($request->get('message'));
        $name = Profanity::hasProfanity($request->get('name'));
        if ($message !== null || $name !== null){
            if ($message !== null){
                $request->request->set('message', $message);
            }
            if ($name !== null){
                $request->request->set('name', $name);
            }

            return redirect()->back()->withInput($request->all())->with('profanity', true);
        }

        $comment = new Comment();
        $comment->name = $request->get('name');
        $comment->email = $request->get('email');
        $comment->comment = $request->get('message');
        $comment->faerie_id = $faerie->id;
        $comment->save();

        return redirect()->back()->with('messageSent', true);
    }

    public function createReply(Request $request, $id){

        $parentComment = Comment::find($id);
        if ($parentComment->faerie->user_id != Auth::id()){
            abort(403);
        }

        $message = Profanity::hasProfanity($request->get('message'));
        if ($message !== null){
            $request->request->set('message', $message);
            return redirect()->back()->withInput($request->all())->with('profanity', true);
        }

        $comment = new Comment();
        $comment->comment = $request->get('message');
        $comment->faerie_id = $parentComment->faerie_id;
        $comment->parent_id = $parentComment->id;
        $comment->name = $parentComment->faerie->name;
        $comment->save();

        return redirect()->back()->with('messageSent', true);
    }
}
