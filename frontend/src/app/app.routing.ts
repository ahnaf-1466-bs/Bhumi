import { Route } from '@angular/router';
import { InitialDataResolver } from 'app/app.resolvers';
import { AuthGuard } from 'app/core/auth/guards/auth.guard';
import { NoAuthGuard } from 'app/core/auth/guards/noAuth.guard';
import { LayoutComponent } from 'app/layout/layout.component';
import { OauthLoginComponent } from './modules/admin/Login/oauth-login/oauth-login.component';
import { ProfileCompletionGuard } from './services/profile-completion.guard';

export const appRoutes: Route[] = [
    // Redirect empty path to '/home'
    // { path: '', pathMatch: 'full', redirectTo: 'home' },

    {
        path: '',
        component: LayoutComponent,
        data: {
            layout: 'vumi',
        },
        children: [
            {
                path: '',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/home/home.module').then(
                        (m) => m.LandingHomeModule
                    ),
            },
            {
                path: 'profile',

                loadChildren: () =>
                    import('app/modules/landing/profile/profile.module').then(
                        (m) => m.ProfileModule
                    ),
            },
            {
                path: 'dashboard',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import(
                        'app/modules/landing/dashboard/dashboard.module'
                    ).then((m) => m.DashboardModule),
            },
            {
                path: 'pre-registration',
                canActivate: [ProfileCompletionGuard],
                canMatch: [AuthGuard],
                loadChildren: () =>
                    import(
                        'app/modules/admin/pre-registration/pre-registration.module'
                    ).then((m) => m.PreRegistrationModule),
            },
            {
                path: 'wish-list',
                canActivate: [ProfileCompletionGuard],
                canMatch: [AuthGuard],
                loadChildren: () =>
                    import('app/modules/landing/wishlist/wishlist.module').then(
                        (m) => m.WishlistModule
                    ),
            },
            {
                path: 'courses',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import(
                        'app/modules/landing/course-list/course-list.module'
                    ).then((m) => m.CourseListModule),
            },
            {
                path: 'about',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/about/about.module').then(
                        (m) => m.AboutModule
                    ),
            },
            {
                path: 'links',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import(
                        'app/modules/landing/footer-link/footer-link.module'
                    ).then((m) => m.FooterLinkModule),
            },
            {
                path: 'ourTeam',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/teams/teams.module').then(
                        (m) => m.TeamsModule
                    ),
            },
            {
                path: 'certificateVerification',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import(
                        'app/modules/landing/certificate-verification/certificate-verification.module'
                    ).then((m) => m.CertificateVerificationModule),
            },
            {
                path: 'course/:id',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/courses/courses.module').then(
                        (m) => m.CoursesModule
                    ),
            },
            {
                path: 'newsfeed/:id',
                loadChildren: () =>
                    import(
                        'app/modules/landing/newsfeeds/newsfeeds.module'
                    ).then((m) => m.NewsfeedsModule),
            },

            {
                path: 'oauth-login',
                //canActivate: [ProfileCompletionGuard],
                component: OauthLoginComponent,
            },
            {
                path: 'signup',
                canMatch: [NoAuthGuard],
                loadChildren: () =>
                    import(
                        'app/modules/admin/registration/registration.module'
                    ).then((m) => m.RegistrationModule),
            },
            {
                path: 'password',
                loadChildren: () =>
                    import('app/modules/admin/password/password.module').then(
                        (m) => m.PasswordModule
                    ),
            },
        ],
    },

    // Redirect signed-in user to the '/example'
    //
    // After the user signs in, the sign-in page will redirect the user to the 'signed-in-redirect'
    // path. Below is another redirection for that path to redirect the user to the desired
    // location. This is a small convenience to keep all main routes together here on this file.
    { path: 'signed-in-redirect', pathMatch: 'full', redirectTo: 'home' },

    // Auth routes for guests
    {
        path: '',
        component: LayoutComponent,
        data: {
            layout: 'vumi',
        },
        children: [
            {
                path: 'login',
                canMatch: [NoAuthGuard],
                loadChildren: () =>
                    import('app/modules/admin/Login/Login.module').then(
                        (m) => m.LoginModule
                    ),
            },
        ],
    },

    // Landing routes
    {
        path: '',
        component: LayoutComponent,
        data: {
            layout: 'vumi',
        },
        children: [
            {
                path: 'home',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/home/home.module').then(
                        (m) => m.LandingHomeModule
                    ),
            },
            {
                path: 'course',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/courses/courses.module').then(
                        (m) => m.CoursesModule
                    ),
            },
            {
                path: 'enrollment/:id',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import(
                        'app/modules/landing/enrollment/enrollment.module'
                    ).then((m) => m.EnrollmentModule),
            },
            {
                path: 'payment',
                canActivate: [ProfileCompletionGuard],
                loadChildren: () =>
                    import('app/modules/landing/payment/payment.module').then(
                        (m) => m.PaymentModule
                    ),
            },
        ],
    },

    // course activity paths are placed here
    // {
    //     path: '',
    //     component: LayoutComponent,
    //     data: {
    //         layout: 'vumi',
    //     },
    //     children: [
    //         {
    //             path: 'pdf',
    //             canActivate: [ProfileCompletionGuard],
    //             loadChildren: () =>
    //                 import(
    //                     'app/modules/landing/pdf-activity/pdf-activity.module'
    //                 ).then((m) => m.PdfActivityModule),
    //         },
    //         {
    //             path: 'video',
    //             canActivate: [ProfileCompletionGuard],
    //             loadChildren: () =>
    //                 import(
    //                     'app/modules/landing/video-activity/video-activity.module'
    //                 ).then((m) => m.VideoActivityModule),
    //         },
    //         {
    //             path: 'quiz',
    //             canActivate: [ProfileCompletionGuard],
    //             canMatch: [AuthGuard],
    //             loadChildren: () =>
    //                 import(
    //                     'app/modules/landing/quiz-activity/quiz-acitivity.module'
    //                 ).then((m) => m.QuizActivityModule),
    //         },
    //         {
    //             path: 'feedback',
    //             canActivate: [ProfileCompletionGuard],
    //             loadChildren: () =>
    //                 import(
    //                     'app/modules/landing/feedback-activity/feedback-activity.module'
    //                 ).then((m) => m.FeedbackActivityModule),
    //         },
    //         {
    //             path: 'meeting',
    //             canActivate: [ProfileCompletionGuard],
    //             loadChildren: () =>
    //                 import('app/modules/landing/meet/meet.module').then(
    //                     (m) => m.MeetModule
    //                 ),
    //         },
    //     ]
    // },

    // course activity design
    {
        path: 'course-activity/:id',
        component: LayoutComponent,
        canMatch: [AuthGuard],
        data: {
            layout: 'vumi',
        },
        children: [
            {
                path: '',
                loadChildren: () =>
                    import(
                        'app/modules/landing/course-activity/course-activity.module'
                    ).then((m) => m.CourseActivityModule),
            },
        ]
    },

    // Admin routes
    {
        path: '',
        component: LayoutComponent,
        data: {
            layout: 'vumi',
        },
        resolve: {
            initialData: InitialDataResolver,
        },
        children: [
            {
                path: 'login',
                canMatch: [NoAuthGuard],
                loadChildren: () =>
                    import('app/modules/admin/Login/Login.module').then(
                        (m) => m.LoginModule
                    ),
            },
        ],
    },

    { path: '**', pathMatch: 'full', redirectTo: '' },
];
