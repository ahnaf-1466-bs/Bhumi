import { Route } from '@angular/router';
import { CourseActivityComponent } from './course-activity.component';
import { ProfileCompletionGuard } from 'app/services/profile-completion.guard';

export const CourseActivityRoutes: Route[] = [
    {
        path: '',
        canActivate: [ProfileCompletionGuard],
        component: CourseActivityComponent,
        children: [
            {
                path: 'pdf',
                loadChildren: () =>
                    import(
                        'app/modules/landing/pdf-activity/pdf-activity.module'
                    ).then((m) => m.PdfActivityModule),
            },
            {
                path: 'video',
                loadChildren: () =>
                    import(
                        'app/modules/landing/video-activity/video-activity.module'
                    ).then((m) => m.VideoActivityModule),
            },
            {
                path: 'video-pdf',
                loadChildren: () =>
                    import(
                        'app/modules/landing/video-pdf-activity/video-pdf-activity.module'
                    ).then((m) => m.VideoPdfActivityModule),
            },
            {
                path: 'quiz',
                loadChildren: () =>
                    import(
                        'app/modules/landing/quiz-activity/quiz-acitivity.module'
                    ).then((m) => m.QuizActivityModule),
            },
            {
                path: 'feedback',
                loadChildren: () =>
                    import(
                        'app/modules/landing/feedback-activity/feedback-activity.module'
                    ).then((m) => m.FeedbackActivityModule),
            },
            {
                path: 'meeting',
                loadChildren: () =>
                    import(
                        'app/modules/landing/meet/meet.module'
                    ).then((m) => m.MeetModule),
            }
            
        ]
    },
];
