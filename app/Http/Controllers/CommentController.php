<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Faerie;
use App\Services\Profanity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $comment->is_faerie = false;
        $comment->comment = $request->get('message');
        $comment->faerie_id = $faerie->id;
        $comment->save();

        Mail::raw("Faerie received a message: ".route('doors.show', $faerie->id), function ($message) use ($faerie){
            $message->to($faerie->user->email)->subject('New message!');
        });
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
        $comment->is_faerie = true;
        $comment->name = $parentComment->faerie->name;
        $comment->save();

        if ($parentComment->email != null){
            Mail::raw("You said: \"".$parentComment->comment."\"\n and ".$parentComment->faerie->name.' replied: "'.$comment->comment.'"', function ($message) use ($parentComment){
                $message->to($parentComment->email)->subject($parentComment->faerie->name.' replied to you!');
            });
        }

        return redirect()->back()->with('messageSent', true);
    }
}
