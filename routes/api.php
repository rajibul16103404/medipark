<?php

use App\Http\Controllers\Api\AboutUsPage2ndAfterOurVisionSectionController;
use App\Http\Controllers\Api\AboutUsPageAfterOurVisionSectionController;
use App\Http\Controllers\Api\AboutUsPageBannerSectionController;
use App\Http\Controllers\Api\AboutUsPageOurMissionSectionController;
use App\Http\Controllers\Api\AboutUsPageOurVisionSectionController;
use App\Http\Controllers\Api\AboutUsPageWhoWeAreSectionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BlogPageBannerSectionController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactPageBannerSectionController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\FooterContactController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\GalleryPageBannerSectionController;
use App\Http\Controllers\Api\HomepageAboutUsSectionController;
use App\Http\Controllers\Api\HomepageCtaSectionController;
use App\Http\Controllers\Api\HomepageHeroSectionController;
use App\Http\Controllers\Api\InstallmentRuleController;
use App\Http\Controllers\Api\InvestorController;
use App\Http\Controllers\Api\InvestorInstallmentController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsPageBannerSectionController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PrivilegeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideoLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password reset routes (public)
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // OTP routes (public)
    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);

    Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

    // Investor login routes (public)
    Route::post('/investor/request-login', [AuthController::class, 'requestInvestorLogin']);
    Route::post('/investor/login', [AuthController::class, 'investorLogin']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Investor authenticated routes
    Route::middleware('auth.investor')->prefix('investor')->group(function () {
        Route::get('/me', [AuthController::class, 'investorMe']);
        Route::post('/logout', [AuthController::class, 'investorLogout']);
        Route::post('/refresh', [AuthController::class, 'investorRefresh']);
    });
});

// Public Routes
Route::get('/homepage-hero-sections/active', [HomepageHeroSectionController::class, 'show']);
Route::get('/homepage-about-us-sections/active', [HomepageAboutUsSectionController::class, 'show']);
Route::get('/homepage-cta-sections/active', [HomepageCtaSectionController::class, 'show']);
Route::get('/about-us-page-banner-sections/active', [AboutUsPageBannerSectionController::class, 'show']);
Route::get('/about-us-page-who-we-are-sections/active', [AboutUsPageWhoWeAreSectionController::class, 'show']);
Route::get('/about-us-page-our-mission-sections/active', [AboutUsPageOurMissionSectionController::class, 'show']);
Route::get('/about-us-page-our-vision-sections/active', [AboutUsPageOurVisionSectionController::class, 'show']);
Route::get('/about-us-page-after-our-vision-sections/active', [AboutUsPageAfterOurVisionSectionController::class, 'show']);
Route::get('/about-us-page-2nd-after-our-vision-sections/active', [AboutUsPage2ndAfterOurVisionSectionController::class, 'show']);
Route::get('/contact-page-banner-sections', [ContactPageBannerSectionController::class, 'index']);
Route::get('/contact-page-banner-sections/active', [ContactPageBannerSectionController::class, 'show']);
Route::get('/about-us-page-banner-sections', [AboutUsPageBannerSectionController::class, 'index']);
Route::get('/about-us-page-banner-sections/active', [AboutUsPageBannerSectionController::class, 'show']);
Route::get('/gallery-page-banner-sections', [GalleryPageBannerSectionController::class, 'index']);
Route::get('/news-page-banner-sections', [NewsPageBannerSectionController::class, 'index']);
Route::get('/blog-page-banner-sections', [BlogPageBannerSectionController::class, 'index']);
Route::get('/video-links', [VideoLinkController::class, 'index']);
Route::get('/galleries', [GalleryController::class, 'index']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{news}', [NewsController::class, 'show']);
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{blog}', [BlogController::class, 'show']);

// Contact routes (Public submission)
Route::post('/contacts', [ContactController::class, 'store']);

// Public Footer Contact route
Route::get('/footer-contact', [FooterContactController::class, 'show']);

// Public Facilities route
Route::get('/facilities', [FacilityController::class, 'index']);

// Public Social Links route
Route::get('/social-links', [SocialLinkController::class, 'index']);

// Public Branches route
Route::get('/branches', [BranchController::class, 'index']);

// Public Doctors route (Public)


Route::middleware('auth:api')->group(function () {
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->middleware('privilege:read-profile');
        Route::put('/', [ProfileController::class, 'update'])->middleware('privilege:update-profile');
    });

    // Privilege routes
    Route::prefix('privileges')->group(function () {
        Route::get('/', [PrivilegeController::class, 'index'])->middleware('privilege:read-privileges,read-roles,assign-privileges-to-roles');
        Route::get('/{privilege}', [PrivilegeController::class, 'show'])->middleware('privilege:read-privileges,read-roles,assign-privileges-to-roles');
    });

    // Role routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('privilege:read-roles,assign-privileges-to-roles');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('privilege:read-roles,assign-privileges-to-roles');
        Route::post('/', [RoleController::class, 'store'])->middleware('privilege:create-roles');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('privilege:update-roles');
        Route::patch('/{role}', [RoleController::class, 'update'])->middleware('privilege:update-roles');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('privilege:delete-roles');
        Route::post('/{role}/privileges', [RoleController::class, 'assignPrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges', [RoleController::class, 'removePrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges/{privilege}', [RoleController::class, 'removePrivilege'])->middleware('privilege:assign-privileges-to-roles');
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('privilege:read-users');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('privilege:read-users');
        Route::post('/', [UserController::class, 'store'])->middleware('privilege:create-users');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('privilege:update-users');
        Route::patch('/{user}', [UserController::class, 'update'])->middleware('privilege:update-users');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('privilege:delete-users');
        Route::post('/{user}/suspend', [UserController::class, 'suspend'])->middleware('privilege:suspend-users');
        Route::post('/{user}/unsuspend', [UserController::class, 'unsuspend'])->middleware('privilege:suspend-users');
    });

    // Homepage Hero Section routes (Admin)
    Route::prefix('homepage-hero-sections')->group(function () {
        Route::get('/', [HomepageHeroSectionController::class, 'index'])->middleware('privilege:read-homepage-hero-sections');
        Route::get('/{homepageHeroSection}', [HomepageHeroSectionController::class, 'showById'])->middleware('privilege:read-homepage-hero-sections');
        Route::post('/', [HomepageHeroSectionController::class, 'store'])->middleware('privilege:create-homepage-hero-sections');
        Route::post('/{homepageHeroSection}', [HomepageHeroSectionController::class, 'update'])->middleware('privilege:update-homepage-hero-sections');
        Route::delete('/{homepageHeroSection}', [HomepageHeroSectionController::class, 'destroy'])->middleware('privilege:delete-homepage-hero-sections');
        Route::post('/{homepageHeroSection}/set-active', [HomepageHeroSectionController::class, 'setActive'])->middleware('privilege:update-homepage-hero-sections');
    });

    // Homepage About Us Section routes (Admin)
    Route::prefix('homepage-about-us-sections')->group(function () {
        Route::get('/', [HomepageAboutUsSectionController::class, 'index'])->middleware('privilege:read-homepage-about-us-sections');
        Route::get('/{homepageAboutUsSection}', [HomepageAboutUsSectionController::class, 'showById'])->middleware('privilege:read-homepage-about-us-sections');
        Route::post('/', [HomepageAboutUsSectionController::class, 'store'])->middleware('privilege:create-homepage-about-us-sections');
        Route::post('/{homepageAboutUsSection}', [HomepageAboutUsSectionController::class, 'update'])->middleware('privilege:update-homepage-about-us-sections');
        Route::delete('/{homepageAboutUsSection}', [HomepageAboutUsSectionController::class, 'destroy'])->middleware('privilege:delete-homepage-about-us-sections');
        Route::post('/{homepageAboutUsSection}/set-active', [HomepageAboutUsSectionController::class, 'setActive'])->middleware('privilege:update-homepage-about-us-sections');
    });

    // Homepage CTA Section routes (Admin)
    Route::prefix('homepage-cta-sections')->group(function () {
        Route::get('/', [HomepageCtaSectionController::class, 'index'])->middleware('privilege:read-homepage-cta-sections');
        Route::get('/{homepageCtaSection}', [HomepageCtaSectionController::class, 'showById'])->middleware('privilege:read-homepage-cta-sections');
        Route::post('/', [HomepageCtaSectionController::class, 'store'])->middleware('privilege:create-homepage-cta-sections');
        Route::post('/{homepageCtaSection}', [HomepageCtaSectionController::class, 'update'])->middleware('privilege:update-homepage-cta-sections');
        Route::delete('/{homepageCtaSection}', [HomepageCtaSectionController::class, 'destroy'])->middleware('privilege:delete-homepage-cta-sections');
        Route::post('/{homepageCtaSection}/set-active', [HomepageCtaSectionController::class, 'setActive'])->middleware('privilege:update-homepage-cta-sections');
    });

    // About Us Page Banner Section routes (Admin) - Singleton pattern
    Route::prefix('about-us-page-banner-sections')->group(function () {
        Route::post('/', [AboutUsPageBannerSectionController::class, 'store'])->middleware('privilege:create-about-us-page-banner-sections');
        Route::post('/set-active', [AboutUsPageBannerSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-banner-sections');
    });

    // Gallery Page Banner Section routes (Admin) - Singleton pattern
    Route::prefix('gallery-page-banner-sections')->group(function () {
        Route::post('/', [GalleryPageBannerSectionController::class, 'store'])->middleware('privilege:create-gallery-page-banner-sections');
        Route::post('/set-active', [GalleryPageBannerSectionController::class, 'setActive'])->middleware('privilege:update-gallery-page-banner-sections');
    });

    // News Page Banner Section routes (Admin) - Singleton pattern
    Route::prefix('news-page-banner-sections')->group(function () {
        Route::post('/', [NewsPageBannerSectionController::class, 'store'])->middleware('privilege:create-news-page-banner-sections');
        Route::post('/set-active', [NewsPageBannerSectionController::class, 'setActive'])->middleware('privilege:update-news-page-banner-sections');
    });

    // Blog Page Banner Section routes (Admin) - Singleton pattern
    Route::prefix('blog-page-banner-sections')->group(function () {
        Route::post('/', [BlogPageBannerSectionController::class, 'store'])->middleware('privilege:create-blog-page-banner-sections');
        Route::post('/set-active', [BlogPageBannerSectionController::class, 'setActive'])->middleware('privilege:update-blog-page-banner-sections');
    });

    // About Us Page Who We Are Section routes (Admin)
    Route::prefix('about-us-page-who-we-are-sections')->group(function () {
        Route::get('/', [AboutUsPageWhoWeAreSectionController::class, 'index'])->middleware('privilege:read-about-us-page-who-we-are-sections');
        Route::get('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-who-we-are-sections');
        Route::post('/', [AboutUsPageWhoWeAreSectionController::class, 'store'])->middleware('privilege:create-about-us-page-who-we-are-sections');
        Route::post('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'update'])->middleware('privilege:update-about-us-page-who-we-are-sections');
        Route::patch('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'update'])->middleware('privilege:update-about-us-page-who-we-are-sections');
        Route::delete('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-who-we-are-sections');
        Route::post('/{aboutUsPageWhoWeAreSection}/set-active', [AboutUsPageWhoWeAreSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-who-we-are-sections');
    });

    // About Us Page Our Mission Section routes (Admin)
    Route::prefix('about-us-page-our-mission-sections')->group(function () {
        Route::get('/', [AboutUsPageOurMissionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-our-mission-sections');
        Route::get('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-our-mission-sections');
        Route::post('/', [AboutUsPageOurMissionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-our-mission-sections');
        Route::post('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-mission-sections');
        Route::patch('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-mission-sections');
        Route::delete('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-our-mission-sections');
        Route::post('/{aboutUsPageOurMissionSection}/set-active', [AboutUsPageOurMissionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-our-mission-sections');
    });

    // About Us Page Our Vision Section routes (Admin)
    Route::prefix('about-us-page-our-vision-sections')->group(function () {
        Route::get('/', [AboutUsPageOurVisionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-our-vision-sections');
        Route::get('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-our-vision-sections');
        Route::post('/', [AboutUsPageOurVisionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-our-vision-sections');
        Route::post('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-vision-sections');
        Route::patch('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-vision-sections');
        Route::delete('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-our-vision-sections');
        Route::post('/{aboutUsPageOurVisionSection}/set-active', [AboutUsPageOurVisionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-our-vision-sections');
    });

    // About Us Page After Our Vision Section routes (Admin)
    Route::prefix('about-us-page-after-our-vision-sections')->group(function () {
        Route::get('/', [AboutUsPageAfterOurVisionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-after-our-vision-sections');
        Route::get('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-after-our-vision-sections');
        Route::post('/', [AboutUsPageAfterOurVisionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-after-our-vision-sections');
        Route::post('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-after-our-vision-sections');
        Route::patch('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-after-our-vision-sections');
        Route::delete('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-after-our-vision-sections');
        Route::post('/{aboutUsPageAfterOurVisionSection}/set-active', [AboutUsPageAfterOurVisionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-after-our-vision-sections');
    });

    // About Us Page 2nd After Our Vision Section routes (Admin)
    Route::prefix('about-us-page-2nd-after-our-vision-sections')->group(function () {
        Route::get('/', [AboutUsPage2ndAfterOurVisionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-2nd-after-our-vision-sections');
        Route::get('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-2nd-after-our-vision-sections');
        Route::post('/', [AboutUsPage2ndAfterOurVisionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-2nd-after-our-vision-sections');
        Route::post('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-2nd-after-our-vision-sections');
        Route::patch('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-2nd-after-our-vision-sections');
        Route::delete('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-2nd-after-our-vision-sections');
        Route::post('/{section}/set-active', [AboutUsPage2ndAfterOurVisionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-2nd-after-our-vision-sections');
    });

    // Contact routes (Admin)
    Route::prefix('contacts')->group(function () {
        // Route::get('/', [ContactController::class, 'index'])->middleware('privilege:read-contacts');
        Route::get('/{contact}', [ContactController::class, 'show'])->middleware('privilege:read-contacts');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->middleware('privilege:delete-contacts');
    });

    // Contact Page Banner Section routes (Admin) - Singleton pattern
    Route::prefix('contact-page-banner-sections')->group(function () {
        Route::post('/', [ContactPageBannerSectionController::class, 'store'])->middleware('privilege:create-contact-page-banner-sections');
        Route::post('/set-active', [ContactPageBannerSectionController::class, 'setActive'])->middleware('privilege:update-contact-page-banner-sections');
    });

    // Video Link routes (Admin) - Singleton pattern
    Route::prefix('video-links')->group(function () {
        Route::post('/', [VideoLinkController::class, 'store'])->middleware('privilege:create-video-links');
        Route::post('/set-active', [VideoLinkController::class, 'setActive'])->middleware('privilege:update-video-links');
    });

    // Gallery routes (Admin)
    Route::prefix('galleries')->group(function () {
        // Route::get('/{gallery}', [GalleryController::class, 'show'])->middleware('privilege:read-galleries');
        Route::post('/', [GalleryController::class, 'store'])->middleware('privilege:create-galleries');
        Route::post('/{gallery}', [GalleryController::class, 'update'])->middleware('privilege:update-galleries');
        Route::patch('/{gallery}', [GalleryController::class, 'update'])->middleware('privilege:update-galleries');
        Route::delete('/{gallery}', [GalleryController::class, 'destroy'])->middleware('privilege:delete-galleries');
        Route::post('/{gallery}/set-active', [GalleryController::class, 'setActive'])->middleware('privilege:update-galleries');
    });

    // News routes (Admin)
    Route::prefix('news')->group(function () {
        Route::post('/', [NewsController::class, 'store'])->middleware('privilege:create-news');
        Route::post('/{news}', [NewsController::class, 'update'])->middleware('privilege:update-news');
        Route::patch('/{news}', [NewsController::class, 'update'])->middleware('privilege:update-news');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->middleware('privilege:delete-news');
        Route::post('/{news}/set-active', [NewsController::class, 'setActive'])->middleware('privilege:update-news');
    });

    // Blog routes (Admin)
    Route::prefix('blogs')->group(function () {
        Route::post('/', [BlogController::class, 'store'])->middleware('privilege:create-blogs');
        Route::post('/{blog}', [BlogController::class, 'update'])->middleware('privilege:update-blogs');
        Route::patch('/{blog}', [BlogController::class, 'update'])->middleware('privilege:update-blogs');
        Route::delete('/{blog}', [BlogController::class, 'destroy'])->middleware('privilege:delete-blogs');
        Route::post('/{blog}/set-active', [BlogController::class, 'setActive'])->middleware('privilege:update-blogs');
    });

    // Investor routes (Admin)
    Route::prefix('investors')->group(function () {
        Route::post('/', [InvestorController::class, 'store'])->middleware('privilege:create-investors');
        // Route::get('/', [InvestorController::class, 'index'])->middleware('privilege:read-investors');
        // Route::get('/{investor}', [InvestorController::class, 'show'])->middleware('privilege:read-investors');
        Route::post('/{investor}', [InvestorController::class, 'update'])->middleware('privilege:update-investors');
        Route::patch('/{investor}', [InvestorController::class, 'update'])->middleware('privilege:update-investors');
        Route::delete('/{investor}', [InvestorController::class, 'destroy'])->middleware('privilege:delete-investors');
    });

    // Investor Installment routes (Admin)
    Route::prefix('investor-installments')->group(function () {
        Route::post('/', [InvestorInstallmentController::class, 'store'])->middleware('privilege:create-investor-installments');
        // Route::get('/', [InvestorInstallmentController::class, 'index'])->middleware('privilege:read-investor-installments');
        // Route::get('/{investorInstallment}', [InvestorInstallmentController::class, 'show'])->middleware('privilege:read-investor-installments');
        Route::post('/{investorInstallment}/process-payment', [InvestorInstallmentController::class, 'processPayment'])->middleware('privilege:update-investor-installments');
        Route::post('/{investorInstallment}', [InvestorInstallmentController::class, 'update'])->middleware('privilege:update-investor-installments');
        Route::patch('/{investorInstallment}', [InvestorInstallmentController::class, 'update'])->middleware('privilege:update-investor-installments');
        Route::delete('/{investorInstallment}', [InvestorInstallmentController::class, 'destroy'])->middleware('privilege:delete-investor-installments');
    });

    // Doctor routes (Admin)
    Route::prefix('doctors')->group(function () {
        // Route::get('/', [DoctorController::class, 'index'])->middleware('privilege:read-doctors');
        // Route::get('/{doctor}', [DoctorController::class, 'show'])->middleware('privilege:read-doctors');
        Route::post('/', [DoctorController::class, 'store'])->middleware('privilege:create-doctors');
        Route::post('/{doctor}', [DoctorController::class, 'update'])->middleware('privilege:update-doctors');
        Route::patch('/{doctor}', [DoctorController::class, 'update'])->middleware('privilege:update-doctors');
        Route::delete('/{doctor}', [DoctorController::class, 'destroy'])->middleware('privilege:delete-doctors');
    });

    // Facility routes (Admin)
    Route::prefix('facilities')->group(function () {
        // Route::get('/{facility}', [FacilityController::class, 'show'])->middleware('privilege:read-facilities');
        Route::post('/', [FacilityController::class, 'store'])->middleware('privilege:create-facilities');
        Route::post('/{facility}', [FacilityController::class, 'update'])->middleware('privilege:update-facilities');
        Route::patch('/{facility}', [FacilityController::class, 'update'])->middleware('privilege:update-facilities');
        Route::delete('/{facility}', [FacilityController::class, 'destroy'])->middleware('privilege:delete-facilities');
        Route::post('/{facility}/set-active', [FacilityController::class, 'setActive'])->middleware('privilege:update-facilities');
    });

    // Footer Contact routes (Admin) - Singleton (only one record)
    Route::prefix('footer-contact')->group(function () {
        Route::post('/', [FooterContactController::class, 'store'])->middleware('privilege:update-footer-contact');
    });

    // Social Link routes (Admin)
    Route::prefix('social-links')->group(function () {
        // Route::get('/{socialLink}', [SocialLinkController::class, 'show'])->middleware('privilege:read-social-links');
        Route::post('/', [SocialLinkController::class, 'store'])->middleware('privilege:create-social-links');
        Route::post('/{socialLink}', [SocialLinkController::class, 'update'])->middleware('privilege:update-social-links');
        Route::patch('/{socialLink}', [SocialLinkController::class, 'update'])->middleware('privilege:update-social-links');
        Route::delete('/{socialLink}', [SocialLinkController::class, 'destroy'])->middleware('privilege:delete-social-links');
        Route::post('/{socialLink}/set-active', [SocialLinkController::class, 'setActive'])->middleware('privilege:update-social-links');
    });

    // Branch routes (Admin)
    Route::prefix('branches')->group(function () {
        // Route::get('/{branch}', [BranchController::class, 'show'])->middleware('privilege:read-branches');
        Route::post('/', [BranchController::class, 'store'])->middleware('privilege:create-branches');
        Route::post('/{branch}', [BranchController::class, 'update'])->middleware('privilege:update-branches');
        Route::patch('/{branch}', [BranchController::class, 'update'])->middleware('privilege:update-branches');
        Route::delete('/{branch}', [BranchController::class, 'destroy'])->middleware('privilege:delete-branches');
        Route::post('/{branch}/set-active', [BranchController::class, 'setActive'])->middleware('privilege:update-branches');
    });

    // Installment Rule routes (Admin)
    Route::prefix('installment-rules')->group(function () {
        Route::get('/', [InstallmentRuleController::class, 'index'])->middleware('privilege:read-installment-rules');
        Route::get('/{installmentRule}', [InstallmentRuleController::class, 'show'])->middleware('privilege:read-installment-rules');
        Route::post('/', [InstallmentRuleController::class, 'store'])->middleware('privilege:create-installment-rules');
        Route::post('/{installmentRule}', [InstallmentRuleController::class, 'update'])->middleware('privilege:update-installment-rules');
        Route::patch('/{installmentRule}', [InstallmentRuleController::class, 'update'])->middleware('privilege:update-installment-rules');
        Route::delete('/{installmentRule}', [InstallmentRuleController::class, 'destroy'])->middleware('privilege:delete-installment-rules');
        Route::post('/{installmentRule}/set-active', [InstallmentRuleController::class, 'setActive'])->middleware('privilege:update-installment-rules');
    });
});


Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/investors', [InvestorController::class, 'index']);
Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
Route::get('/investors/{investor}', [InvestorController::class, 'show']);
