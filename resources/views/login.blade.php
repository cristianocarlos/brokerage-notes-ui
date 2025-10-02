<x-layout>
    <form
        action="{{ route('login.attempt') }}"
        class="[&>input]:border"
        method="post"
    >
        @csrf
        <x-form-errors />
        <input name="email" type="email" placeholder="E-mail" />
        <input name="password" type="password" placeholder="Password" />
        <button class="bg-amber-400" type="submit">Submit</button>
    </form>
</x-layout>

