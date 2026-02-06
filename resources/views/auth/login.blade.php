@extends('layouts.auth-layout')
@section('title', 'Register & Login')
@section('content')


<div class="container" style="min-height: 100vh;">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh; padding: 20px 0;">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            
            <!-- Card Container -->
            <div class="card shadow-lg" style="background-color: #2d2d2d; border: 1px solid #404040; border-radius: 15px; overflow: hidden;">
                
                <!-- Header -->
                <div class="card-header text-center" style="background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%); border-bottom: 2px solid #404040; padding: 30px 20px;">
                    <h2 class="mb-0" style="color: #ffffff; font-weight: 600; letter-spacing: 1px;">
                        <i class="bi bi-shield-lock" style="color: #e0e0e0;"></i> Welcome
                    </h2>
                    <p class="mb-0 mt-2" style="color: #b0b0b0; font-size: 14px;">Sign in to continue or create an account</p>
                </div>
                
                <!-- Card Body -->
                <div class="card-body" style="padding: 40px 30px;">
                    
                    <!-- Toggle Buttons -->
                    <div class="d-flex mb-4 rounded" style="background-color: #1a1a1a; padding: 5px; border: 1px solid #404040;">
                        <button class="btn flex-fill active" id="loginTab" onclick="showLogin()" style="background-color: #ffffff; color: #000000; border: none; border-radius: 5px; padding: 12px; font-weight: 600; transition: all 0.3s;">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                        <button class="btn flex-fill" id="registerTab" onclick="showRegister()" style="background-color: transparent; color: #b0b0b0; border: none; border-radius: 5px; padding: 12px; font-weight: 600; transition: all 0.3s;">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>
                    
                    <!-- Login Form -->
                    <div id="loginForm">
                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" style="color: #e0e0e0; font-weight: 500;">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email"  style="background-color: #1a1a1a; border: 1px solid #404040; color: #ffffff; padding: 12px 15px; border-radius: 8px;">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" style="color: #e0e0e0; font-weight: 500;">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <div class="position-relative">
                                    <input name="password" type="password" class="form-control" id="loginPassword" placeholder="Enter your password"  style="background-color: #1a1a1a; border: 1px solid #404040; color: #ffffff; padding: 12px 15px; border-radius: 8px; padding-right: 45px;">
                                    <button type="button" class="btn btn-link position-absolute" onclick="togglePassword('loginPassword', 'loginEye')" style="right: 5px; top: 50%; transform: translateY(-50%); color: #b0b0b0; text-decoration: none;">
                                        <i class="bi bi-eye" id="loginEye"></i>
                                    </button>
                                </div>
                            </div>
                            
                          
                            
                            <button type="submit" class="btn w-100 mb-3" style="background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%); color: #000000; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 16px; transition: all 0.3s;">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        
                        </form>
                    </div>
                    
                    <!-- Register Form -->
                    <div id="registerForm" style="display: none;">
                        <form method="POST" action="{{ route('register.post') }}">
                          @csrf
                            <div class="mb-3">
                                <label class="form-label" style="color: #e0e0e0; font-weight: 500;">
                                    <i class="bi bi-person"></i> Full Name
                                </label>
                                <input name="name" type="text" class="form-control" placeholder="Enter your full name"  style="background-color: #1a1a1a; border: 1px solid #404040; color: #ffffff; padding: 12px 15px; border-radius: 8px;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="color: #e0e0e0; font-weight: 500;">
                                    <i class="bi bi-person"></i> User Name
                                </label>
                                <input name="username" type="text" class="form-control" placeholder="Enter your full name"  style="background-color: #1a1a1a; border: 1px solid #404040; color: #ffffff; padding: 12px 15px; border-radius: 8px;">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" style="color: #e0e0e0; font-weight: 500;">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email"  style="background-color: #1a1a1a; border: 1px solid #404040; color: #ffffff; padding: 12px 15px; border-radius: 8px;">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" style="color: #e0e0e0; font-weight: 500;">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <div class="position-relative">
                                    <input name="password" type="password" class="form-control" id="registerPassword" placeholder="Create a password"  style="background-color: #1a1a1a; border: 1px solid #404040; color: #ffffff; padding: 12px 15px; border-radius: 8px; padding-right: 45px;">
                                    <button type="button" class="btn btn-link position-absolute" onclick="togglePassword('registerPassword', 'registerEye')" style="right: 5px; top: 50%; transform: translateY(-50%); color: #b0b0b0; text-decoration: none;">
                                        <i class="bi bi-eye" id="registerEye"></i>
                                    </button>
                                </div>
                                <small style="color: #808080; font-size: 12px;">Must be at least 8 characters</small>
                            </div>
                            
                         
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="agreeTerms"  style="background-color: #1a1a1a; border: 1px solid #404040;">
                                <label class="form-check-label" for="agreeTerms" style="color: #b0b0b0; font-size: 14px;">
                                    I agree to the <a href="#" style="color: #e0e0e0; text-decoration: none;">Terms & Conditions</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn w-100 mb-3" style="background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%); color: #000000; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 16px; transition: all 0.3s;">
                                <i class="bi bi-person-plus"></i> Create Account
                            </button>
                            
                          
                        </form>
                    </div>
                    
                </div>
                
               
            </div>
        </div>
    </div>
</div>
@endsection
