<!doctype html>

<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
        <title>Kora 3 - {{ $page_title }}</title>

        @if(isInstalled())
            @yield('stylesheets')
        @endif

        <link rel="stylesheet" href="{{url('assets/css/app.css')}}">
    </head>
    <body class="{{ str_hyphenated($page_class) }}-body {{ ((Auth::guest() || !Auth::user()->active) && isInstalled()) ? 'auth-body' : '' }}">
      @include('partials.nav')
      @include('partials.sideMenu')

      <div class="{{ str_hyphenated($page_class) }} {{ ((Auth::guest() || !Auth::user()->active) && isInstalled()) ? 'auth' : '' }}">
        @yield('header')
        @yield('body')
        @yield('footer')

        @if((Auth::guest() || !Auth::user()->active) && isInstalled())
          @include('partials.footer')
        @endif
      </div>

      @yield('javascripts')

      @if(Auth::guest() || !Auth::user()->active)
        @include('partials.auth.javascripts')
        <script>
          var langURL ="{{action('WelcomeController@setTemporaryLanguage')}}";

          function setTempLang(selected_lang){
            console.log("Language change started: "+langURL);
            $.ajax({
              url:langURL,
              method:'POST',
              data: {
                "_token": "{{ csrf_token() }}",
                "templanguage": selected_lang
              },
              success: function(data){
                location.reload();
              }
            });
          }

          Kora.Auth.Auth();
        </script>
      @endif
	   <div id="preloader" style="display:none;">
         <div id="loader-background">
		   <div id="loader">
		   
		   </div>
		 </div>
       </div>
    </body>
</html>
