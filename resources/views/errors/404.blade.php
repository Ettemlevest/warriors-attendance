<!DOCTYPE html>
<html class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('/js/app.js') }}" defer></script>
    @routes
</head>
<body class="font-sans leading-none text-gray-800 antialiased">

<div class="p-6 bg-indigo-700 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-sm">
      <!-- <logo class="block mx-auto w-full max-w-xs fill-white" height="50" /> -->
      <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">404</h1>
          <div class="mt-8 text-center text-3xl">Nem található a kért oldal!</div>
        </div>
        <div class="px-10 py-4 bg-gray-300 border-t border-gray-300 flex justify-between items-center">
        </div>
      </form>
    </div>
  </div>

</body>
</html>
