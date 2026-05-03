<?php $__env->startSection('title', 'STEAM XRS Manager'); ?>
<?php $__env->startSection('content'); ?>


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
                        <form method="POST" action="<?php echo e(route('login.post')); ?>">
                            <?php echo csrf_field(); ?>
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
                            
                          
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="6LduRqgsAAAAAHoloR7N1fzsylmAAgujkFvwYBmg"></div>
                            </div>
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                            <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <small class="text-danger"><?php echo e($message); ?></small>
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <button type="submit" class="btn w-100 mb-3" style="background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%); color: #000000; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 16px; transition: all 0.3s;">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        
                        </form>
                    </div>
                    
                    <!-- Register Form -->
                    <div id="registerForm" style="display: none;">
                        <form method="POST" action="<?php echo e(route('register.post')); ?>">
                          <?php echo csrf_field(); ?>
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
                                <input class="form-check-input" type="checkbox" id="agreeTerms" style="background-color: #1a1a1a; border: 1px solid #404040;" required>
                                <label class="form-check-label" for="agreeTerms" style="color: #b0b0b0; font-size: 14px;">
                                    I agree to the <a href="#termsModal" data-bs-toggle="modal" style="color: #fff; text-decoration: underline;font-weight:500">Terms & Conditions</a>
                                </label>
                            </div>
                            
                        <!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content" style="background-color: #0d1117; color: #b0b0b0; border-radius: 14px; border: 1px solid #21262d;">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Terms & Conditions</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="font-size: 14px; line-height: 1.6;">
          <p><strong>Last Updated:</strong> April 23, 2021</p>
  
          <p>Please read these Terms of Use (“Terms”) carefully before using the <strong>steamyourdream.org</strong> website (the “Service”) operated by <strong>STEAM Your Dreams, Inc.</strong> (“us”, “we”, or “our”). Your access and use of the Service are conditioned on your acceptance of and compliance with these Terms. By accessing or using the Service, you agree to be bound by these Terms. If you do not agree with any part, you may not access or use the Service.</p>
  
          <h6>1. Accounts</h6>
          <p>When creating an account, you must provide accurate, complete, and up-to-date information. You are responsible for safeguarding your password and any activities that occur under your account. You must not share your password with anyone. Notify us immediately if you suspect unauthorized access or a breach of security.</p>
  
          <h6>2. Intellectual Property</h6>
          <p>All content, features, and functionality of the Service, including but not limited to text, graphics, logos, and software, are the exclusive property of <strong>STEAM Your Dreams, Inc.</strong> and its licensors. You may not copy, reproduce, modify, distribute, or exploit any part of the Service without prior written consent.</p>
  
          <h6>3. Links to Third-Party Websites</h6>
          <p>The Service may contain links to third-party websites or services. We do not control these sites and are not responsible for their content, privacy policies, or practices. You acknowledge and agree that <strong>STEAM Your Dreams, Inc.</strong> is not liable for any damages or losses caused by use of third-party content or services. We encourage you to review the terms and privacy policies of any external sites you visit.</p>
  
          <h6>4. Termination</h6>
          <p>We may terminate or suspend access to the Service immediately, without notice, for any reason, including violation of these Terms. Certain provisions, including ownership, warranty disclaimers, indemnity, and liability limitations, will survive termination. Upon termination, your right to use the Service ceases immediately. You may terminate your account at any time by discontinuing use of the Service.</p>
  
          <h6>5. Disclaimer</h6>
          <p>The Service is provided “AS IS” and “AS AVAILABLE” without warranties of any kind, express or implied. We do not guarantee uninterrupted, secure, or error-free access. You assume full responsibility for any risk associated with your use of the Service.</p>
  
          <h6>6. Governing Law</h6>
          <p>These Terms are governed by and construed in accordance with the laws of the United States. Any disputes arising under these Terms shall be resolved in accordance with applicable U.S. law. Failure to enforce any provision does not constitute a waiver of rights.</p>
  
          <h6>7. Changes to Terms</h6>
          <p>We reserve the right to modify or update these Terms at any time. Material changes will be communicated at least 30 days in advance. Continued use of the Service after changes are effective constitutes acceptance of the revised Terms. If you do not agree, please stop using the Service.</p>
  
          <h6>8. Cookies and Privacy</h6>
          <p>The Service may use cookies to enhance user experience and track site usage. By using the Service, you consent to the use of cookies in accordance with our Privacy Policy. We collect, store, and use information in compliance with applicable privacy laws to provide and improve our Service.</p>
  
          <h6>9. User Obligations</h6>
          <p>You agree to use the Service responsibly and comply with all applicable laws. You must not engage in unauthorized access, data mining, spamming, or any activities that may harm the Service or other users. Any violation may result in account termination or legal action.</p>
  
          <h6>10. Contact Us</h6>
          <p>If you have questions about these Terms, please contact us at: <strong>support@steamyourdream.org</strong>.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/auth/login.blade.php ENDPATH**/ ?>