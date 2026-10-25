<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sheetu Food Management Admin Login</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Thoda halka opacity/gradient taaki background image aur clear aur mast dikhe */
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.70), rgba(30, 41, 59, 0.78)),
                        url('https://images.unsplash.com/photo-1543353071-10c8ba85a904?q=80&w=1920&auto=format&fit=crop') no-repeat center center / cover;
            padding: 20px;
        }

        .login-wrapper {
            display: flex;
            max-width: 900px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            min-height: 500px;
        }

        .login-left {
            flex: 1;
            background: #fdfcf7 url('https://res.cloudinary.com/daizpwrnu/image/upload/v1792908592/shetu_logo.png') no-repeat center center / contain;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            border-right: 1.5px solid #f1f5f9;
            min-height: 250px;
        }

        .login-right {
            flex: 1.1;
            padding: 42px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .login-right h2 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .login-right .subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin-bottom: 22px;
            font-weight: 500;
        }

        .form-floating-custom {
            margin-bottom: 18px;
            position: relative;
        }

        .form-floating-custom label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom i {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
        }

        .input-group-custom input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .input-group-custom input:focus {
            outline: none;
            background: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .btn-login-submit {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 8px 20px -3px rgba(79, 70, 229, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -2px rgba(79, 70, 229, 0.45);
            opacity: 0.97;
        }

        .quote-tag {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            font-style: italic;
        }

        .brand-footer {
            text-align: center;
            font-size: 11px;
            color: #cbd5e1;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Responsive Mobile Friendly Media Query */
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            .login-wrapper {
                flex-direction: column;
                border-radius: 18px;
            }
            .login-left {
                height: 180px;
                padding: 20px;
                border-right: none;
                border-bottom: 1.5px solid #f1f5f9;
            }
            .login-right {
                padding: 28px 20px;
            }
            .login-right h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <!-- Left Side Background Image Container -->
        <div class="login-left"></div>

        <!-- Right Side Form -->
        <div class="login-right">
            <h2>Admin Sign In</h2>
            <p class="subtitle">Enter your administrative credentials to continue</p>

            {{-- Flash Message Handling --}}
            @if(session('flash'))
                <div class="alert alert-{{ session('flash')['type'] ?? 'danger' }} py-2 px-3 rounded-3 mb-3 font-medium text-center" style="font-size: 13px;">
                    {{ session('flash')['msg'] ?? '' }}
                </div>
            @endif

            {{-- Validation Errors Handling --}}
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger py-2 px-3 rounded-3 mb-3 text-center" style="font-size: 13px;">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" autocomplete="off">
                @csrf

                <div class="form-floating-custom">
                    <label for="username">User ID / Username</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your User ID" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="form-floating-custom">
                    <label for="password">Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your Password" required>
                    </div>
                </div>

                <button class="btn-login-submit" type="submit">
                    <span>LOGIN</span>
                    <i class="bi bi-arrow-right-short fs-4"></i>
                </button>
            </form>

            <div class="quote-tag">Made With Love - Sheetu Food</div>
            <div class="brand-footer">Powered by Pacific NestCode</div>
        </div>
    </div>

</body>
</html>