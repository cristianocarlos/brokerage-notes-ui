<x-layout>
    <h1>Welcome {{ Auth::user()->name }}</h1>
    <form
        action="{{ route('logout') }}"
        class="[&>input]:border"
        method="post"
    >
        @csrf
        <button class="bg-amber-400" type="submit">Logout</button>
    </form>
</x-layout>
