<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shiloh Learning Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Shiloh Learning Center</h1>
            <p class="text-gray-600">Therapy & Learning Management System</p>
        </div>

        <!-- Login Options -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Parent Portal -->
            <a href="/parent" class="group">
                <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-blue-500">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-500 transition-colors">
                            <svg class="w-10 h-10 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Parent Portal</h2>
                        <p class="text-gray-600 mb-4">View your child's therapy sessions, attendance, and payment records</p>
                        <div class="flex items-center text-blue-600 font-semibold group-hover:text-blue-700">
                            <span>Login as Parent</span>
                            <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Admin Portal -->
            <a href="/admin" class="group">
                <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-indigo-500">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-500 transition-colors">
                            <svg class="w-10 h-10 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Admin Portal</h2>
                        <p class="text-gray-600 mb-4">Manage students, therapy sessions, attendance, and billing</p>
                        <div class="flex items-center text-indigo-600 font-semibold group-hover:text-indigo-700">
                            <span>Login as Admin</span>
                            <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-gray-600">
            <p class="text-sm">© {{ date('Y') }} Shiloh Learning Center. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
