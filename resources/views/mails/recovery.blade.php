
<div class="w-full text-center">
    <img src="{{ $message->embed(public_path('media/logo.png')) }}" alt="Logo" style="width: 100px; height: 100%;">
</div>

<p>Recovery Password</p>

<p>Hi, {{ $user->name }}</p>

<a href="http://127.0.0.1:3001/auth/change/{{ $token }}">Reset Password</a>
