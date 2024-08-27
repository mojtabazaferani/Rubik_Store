<?php

namespace App\Http\Controllers;

use App\Http\Requests\RubikRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\ClientMessage;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Product;
use App\Notifications\Information;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

class RubikController extends Controller
{
    public function orginalhome()
    {
        $email = request()->cookie('email');

        $cart = Product::where('email', $email)->first();

        if($cart != null) {

            $count = $cart->number;

            return view('index', compact('count'));

        }else {

            $count = 0;

            return view('index', compact('count'));
             
        }
        
    }

    public function orginalHomeTwo()
    {
        return view('index-2');
    }

    public function products()
    {
        return view('products');
    }

    public function productsList()
    {
        return view('products-list');
    }

    public function product()
    {

        $name = request()->cookie('name');

        $email = request()->cookie('email');

        $comments = DB::table('comments')
        ->select(['comment', 'name'])->get();

        $comments = json_decode($comments, true);

        return view('product', compact('name', 'email', 'comments'));

    }

    public function buy(Request $request)
    {
        $userPurchases = Product::where('email', request()->cookie('email'))->first();

        if($userPurchases != null) {

            $information = [
                'name' => $userPurchases->name,
                'email' => $userPurchases->email,
                'message' => 'A New Purchase Was Registered'
            ];

            $admins = DB::table('clients')
            ->select(['*'])
            ->where('user_type', '=', 'yes')
            ->get();

            Notification::send($admins, new Information($information));

            return redirect()->route('orginal.home');

        }
    }

    public function register()
    {
        return view('register');
    }

    public function store(RubikRequest $request)
    {
        
        $create = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if($create) {

            cookie()->queue('name', $request->name, 10080);

            cookie()->queue('email', $request->email, 10080);

            cookie()->queue('register', 'yes', 10080);

            return redirect()->route('login');

        }
    }

    public function login()
    {
        $registerStatus = request()->cookie('register');

        if($registerStatus != 'yes') {

            return redirect()->route('register');

        }else {
            return view('login');
        }
        
    }

    public function check(Request $request)
    {
    
        $email = $request->email;

        $password = $request->password;

        $client = Client::where('email', $email)->first();

        if($client != null) {

            $clientPassword = $client->password;

            if(Hash::check($password, $clientPassword)) {

                cookie()->queue('login', 'yes', 10080);

                cookie()->queue('name', $client->name, 10080);

                cookie()->queue('email', $client->email, 10080);

                $information = [
                    'name' => $client->name,
                    'email' => $request->email,
                    'message' => 'New Register'
                ];
    
                $admins = Client::where('user_type', 'admin')->first();

                Notification::send($admins, new Information($information));

                $updateLogged = Client::find($client->id);

                $updateLogged->logged = 'yes';

                $updateLogged->save();

                return redirect()->route('profile');

            }else {
                return redirect()->route('login');
            }

        }else {
            return redirect()->route('login');
        }

    }

    public function resetPassword()
    {
        return view('reset-password');
    }

    public function updatePassword(Request $request)
    {
        $email = $request->email;

        $client = Client::where('email', $email)->first();

        if($client != null) {

            $rand = rand(12345678, 99999999);

            Mail::send('reset', ['rand' => $rand], function($message) use ($client) {

                $message->to($client->email, 'Code Online')
                ->subject('Reset Password');
            });

            cookie()->queue('email', $client->email, 10080);

            Session::put('rand', $rand);

            return redirect()->route('change.password');
        }else {
            return redirect()->route('error.404');
        }
    }

    public function changePassword()
    {
        $password = Session::get('rand');

        return view('change-password', compact('password'));
    }

    public function editPassword(Request $request)
    {
        $email = request()->cookie('email');

        $password = $request->password;

        $userUpdate = Client::where('email', $email)->first();

        if($userUpdate != null) {

            if($password == Session::get('rand')) {

                $update = Client::find($userUpdate->id);

                $update->password = Hash::make($request->password);

                $update->save();

                return redirect()->route('login');

            }else {
                return redirect()->route('error.404');
            }

        }else {
            return redirect()->route('error.404');
        }

    }

    public function profile()
    {
        $name = request()->cookie('name');

        $email = request()->cookie('email');

        $loginStatus = request()->cookie('login');

        if($loginStatus != 'yes') {

            return redirect()->route('login');

        }else {

            $client = Client::where('email', $email)->first();

            if($client->user_type == 'member') {

                $route = 'panel.member';

            }

            elseif($client->user_type == 'admin') {

                $route = 'panel.admin';

            }else {

                return redirect()->route('error.404');

            }

            return view('profile.personal-info', compact('name', 'route', 'email'));

        }

    }

    public function panelAdmin()
    {

        $email = request()->cookie('email');

        $client = Client::where('email', $email)
        ->where('user_type', 'admin')
        ->first();

        if($client != null) {

            $name = $client->name;

            $user_type = $client->user_type;

            $unReadyNotifications = Client::find($client->id);

            $unReadyNotificationsCount = count($unReadyNotifications->unreadNotifications);

            $unReadyNotifications = $client->unreadNotifications;

            return view('profile.panel-admin', compact('name', 'user_type','unReadyNotifications', 'unReadyNotificationsCount'));
        }

    }

    public function panelMember()
    {

        $member = Client::where('email', request()->cookie('email'))
        ->where('user_type', 'member')
        ->first();

        if($member != null) {

            $name = $member->name;

            $user_type = $member->user_type;

            return view('panel-member', compact('name', 'user_type'));

        }else {

            return redirect()->route('error.404');

        }
    }

    public function notifications()
    {

        $email = request()->cookie('email');

        $admins = Client::where('email', $email)
        ->where('user_type', 'admin')
        ->first();

        if($admins != null) {

            $name = $admins->name;

            $user_type = $admins->user_type;

            $unReadyNotifications = Client::find($admins->id);

            $unReadyNotificationsCount = count($unReadyNotifications->unreadNotifications);

            $unReadyNotifications = $admins->unreadNotifications;

            return view('profile.notifications', compact('name', 'user_type', 'unReadyNotificationsCount', 'unReadyNotifications'));
        }
        
    }

    public function markAll()
    {
        $admins = Client::where('email', request()->cookie('email'))
        ->where('user_type', 'admin')
        ->first();

        if($admins != null) {
            $unReadyNotifications = Client::find($admins->id);

            $unReadyNotifications->unreadNotifications->markAsRead();

            return redirect()->route('profile');
        }

    }

    public function factors()
    {

        $name = request()->cookie('name');

        return view('profile.factors', compact('name'));

    }

    public function addresses($location = null, $state = null)
    {

        $addresses = DB::table('addresses')
        ->select(['*'])
        ->where('name', '=', request()->cookie('name'))
        ->where('email', '=', request()->cookie('email'))
        ->get();

        if($addresses != null) {

            $addresses = json_decode($addresses, true);

            $addressesCount = count($addresses);

            return view('profile.addresses', compact('addresses', 'addressesCount'));

        }else {
            //
        }

    }

    public function favorites()
    {
        
        $name = request()->cookie('name');

        return view('profile.favorites', compact('name'));

    }

    public function cart()
    {
        $email = request()->cookie('email');

        $client = Client::where('email', $email)->first();

        if($client != null) {

            $id = $client->id;

            return view('cart', compact('id'));
        }else {
            //
        }
    }

    public function purchaseInvoice(Request $request)
    {
        $name = request()->cookie('name');

        $price = $request->price;

        $email = request()->cookie('email');

        $productName = $request->product_name;

        $color = $request->color;

        $warranty = $request->warranty;

        $number = $request->number;

        $state = $request->state;

        $city = $request->city;

        $location = $request->location;

        $buyCheck = Product::where('name', $name)
        ->where('price', $price)
        ->where('email', $email)
        ->where('product_name', $productName)
        ->where('color', $color)
        ->where('warranty', $warranty)
        ->first();

        if($buyCheck != null) {

            $buyCheck->increment('number');

            return redirect()->route('checkout');

        }else {

            $purchaseInvoice = Product::create([
                'name' => $name,
                'price' => $price,
                'email' => $email,
                'product_name' => $productName,
                'color' => $color,
                'warranty' => $warranty,
                'number' => $number,
                'state' => $state,
                'city' => $city,
                'location' => $location
            ]);
    
            if($purchaseInvoice) {
                return redirect()->route('checkout');
            }else {
                dd(false);
            }
        }

        
    }

    public function about()
    {
        return view('about');
    }

    public function faq()
    {
        return view('faq');
    }

    public function blog()
    {
        return view('blog');
    }

    public function blogPost()
    {
        return view('blog-post');
    }

    public function compare()
    {
        return view('compare');
    }

    public function checkout()
    {

        $email = request()->cookie('email');

        $checkoutProduct = Product::where('email', $email)->first();

        if($checkoutProduct != null) {

            $name = $checkoutProduct->name;

            $state = $checkoutProduct->state;

            $city = $checkoutProduct->city;

            $location = $checkoutProduct->location;

            $number = $checkoutProduct->number;

            $price = (int) $checkoutProduct->price;

            $price = $price * $number . ".000.000";

            return view('checkout', compact('name', 'state', 'city', 'location', 'number', 'price'));

        }else {
            //
        }

    }

    public function createAddress(Request $request)
    {
        $create = Address::create([
            'name' => request()->cookie('name'),
            'email' => request()->cookie('email'),
            'state' => $request->state,
            'location' => $request->location,
            'zip_code' => $request->zip_code,
            'receiver' => $request->receiver,
            'tel' => $request->tel
        ]);

        if($create) {
            
            return redirect()->route('profile');

        }else {

            return redirect()->route('error.404');

        }
    }

    public function deleteAddress($location = null, $state = null)
    {

        cookie()->queue('state', $state, 10080);

        cookie()->queue('location', $location, 10080);

        return view('deleted-address');
        
    }

    public function deletedAddress(Request $request)
    {

        $deletedAddress = Address::where('name', request()->cookie('name'))
            ->where('email', request()->cookie('email'))
            ->where('location', request()->cookie('location'))
            ->where('state', request()->cookie('state'))
            ->delete();

            if($deletedAddress) {

                return redirect()->route('profile');

            }else {

                return redirect()->route('error.404');

            }
     
    }

    public function addressChange($location = null, $state = null)
    {
        if(isset($location) && isset($state)) {

            Session::put('location', $location);

            Session::put('state', $state);

            return view('profile.address-change');

        }else {

            return redirect()->route('error.404');

        }
    
    }

    public function addressUpdate(Request $request)
    {

        if(isset($request->yes)) {

            $location = Session::get('location');

            $state = Session::get('state');
    
            $name = request()->cookie('name');
    
            $email = request()->cookie('email');
    
            $changeAddress = Address::where('name', $name)
            ->where('email', $email)
            ->where('state', $state)
            ->where('location', $location)
            ->first();
    
            $productAddress = Product::where('email', $email)
            ->where('name', $name)
            ->first();
    
            if($changeAddress != null && $productAddress != null) {
    
                $changeAddress = Address::find($changeAddress->id);
    
                $productAddress = Product::find($productAddress->id);
    
                if($changeAddress != null && $productAddress) {
    
                    $productAddress->state = $changeAddress->state;
    
                    $productAddress->city = $changeAddress->state;
    
                    $productAddress->location = $changeAddress->location;
    
                    $productAddress->save();
    
                    return redirect()->route('checkout');
    
                }
            }
        }else {

            return redirect()->route('error.404');

        }


    }

    public function editAddress($location, $state)
    {
        Session::put('location', $location);

        Session::put('state', $state);

        return view('profile.edit-address');
    }

    public function editedAddress(Request $request)
    {
        $editAddress = Address::where('name', request()->cookie('name'))
        ->where('email', request()->cookie('email'))
        ->where('state', Session::get('state'))
        ->where('location', Session::get('location'))
        ->first();

        if($editAddress != null) {

            $editAddress = Address::find($editAddress->id);

            if($editAddress != null) {

                $editAddress->name = request()->cookie('name');

                $editAddress->email = request()->cookie('email');

                $editAddress->state = $request->state;

                $editAddress->location = $request->location;

                $editAddress->zip_code = $request->zip_code;

                $editAddress->receiver = $request->receiver;

                $editAddress->tel = $request->tel;

                $editAddress->save();

                return redirect()->route('profile');

            }else {

                return redirect()->route('error.404');

            }
            
        }else {

            return redirect()->route('error.404');

        }
    }

    public function contact()
    {
        return view('contact');
    }

    public function sendMessage(Request $request)
    {
        $email = request()->cookie('email');

        if($email === $request->email) {

            $create = Message::create([
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message
            ]);

            if($create) {

                $information = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'message' => 'A new Message Was Registered'
                ];

                $admins = Client::where('user_type', 'admin')->first();

                Notification::send($admins, new Information($information));

                return redirect()->route('orginal.home');

            }else {
                return redirect()->route('error.404');
            }
        }
    }

    public function error404()
    {
        return view('error-404');
    }

    public function comment(Request $request)
    {
        $comment = Comment::create([
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment
        ]);

        if($comment) {

            $information = [
                'name' => $request->name,
                'email' => $request->email,
                'message' => 'A New Comment Was Registered'
            ];

            $admins = Client::where('user_type', 'admin')
            ->first();

            Notification::send($admins, new Information($information));

            return redirect()->route('product');
        }else {
            //
        }
    }

    public function message()
    {
        return view('send-message');
    }

    public function send(Request $request)
    {
        $email = request()->cookie('email');

        $to = $request->to;

        if($email == $request->from) {

            $create = ClientMessage::create([
                'name' => $request->name,
                'from' => $request->from,
                'to' => $to,
                'subject' => $request->subject,
                'message' => $request->message
            ]);

            if($create) {

                Mail::send('output', ['from' => $request->from], function($message) use ($to) {

                    $message->to($to, 'Code Online')
                    ->subject('Test');
                });

                $receiverUser = Client::where('email', $to)->first();

                $information = [
                    'name' => $request->name,
                    'email' => $request->from,
                    'message' => "You Have Received a New Message From : " . $request->name
                ];

                Notification::send($receiverUser, new Information($information));

                return redirect()->route('orginal.home');

            }else {
                return redirect()->route('send.message');
            }


        }else {
            return redirect()->route('error.404');
        }
    }

    public function userMessage()
    {
        $messages = DB::table('client_messages')
        ->select(['*'])
        ->where('to', '=', request()->cookie('email'))
        ->get();

        if($messages != null) {

            $name = request()->cookie('name');

            $messages = json_decode($messages, true);

            $messagesCount = count($messages);

            return view('messages', compact('name', 'messages', 'messagesCount'));
        }else {
            //
        }

    }

    public function logout()
    {
        $email = request()->cookie('email');

        $userLogout = Client::where('email', $email)->first();

        if($userLogout != null) {

            $updateLogged = Client::find($userLogout->id);

            if($updateLogged != null) {

                $updateLogged->logged = 'no';

                $saving = $updateLogged->save();

                if($saving) {

                    cookie()->queue(cookie()->forget('name'));

                    cookie()->queue(cookie()->forget('email'));

                    cookie()->queue(cookie()->forget('login'));

                    return redirect()->route('login');

                }else {
                    
                    return redirect()->route('error.404');

                }
            }else {

                return redirect()->route('error.404');

            }

        }else {

            return redirect()->route('error.404');

        }

    }
}
