Click here to activate your account: <a href="{{action('Auth\UserController@activate', [\Auth::user()->regtoken])}}">Activate</a>.