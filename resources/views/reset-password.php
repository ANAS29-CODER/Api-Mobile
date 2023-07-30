<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="">

                <h2>Reset Password</h2>
                <form method="POST" action="{{ route('password.update.api') }}">
                    @csrf



                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                    <br>
                    <label for="token">Token:</label>
                    <input type="token" name="token" id="token" required>

                    <br>
                    <label for="password">New Password:</label>
                    <input type="password" name="password" id="password" required>
                    <br>

                    <label for="password_confirmation">Confirm Password:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                    <br>
                    <button type="submit">Reset Password</button>
                </form>

        </div>
    </div>
</x-app-layout>
