<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">Pengaturan akun</p>
            <h1 class="mt-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Keamanan akun</h1>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Informasi akun</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Akun yang sedang digunakan untuk masuk.</p>
                    </div>
                    <span class="w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold capitalize text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">{{ $user->role }}</span>
                </div>
                <dl class="grid gap-4 px-6 py-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 break-all font-medium text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
