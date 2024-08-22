<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Models\StudentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
 
class SendSessionEmailCOntroller extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $order = StudentSession::findOrFail($request->id);
        
        foreach (['mzakharchenko@sbase.team ', 'Wagrant1992@gmail.com'] as $recipient) {
            Mail::to($recipient)->send(new OrderShipped($order));
        }
 
        return redirect('/');
    }
}