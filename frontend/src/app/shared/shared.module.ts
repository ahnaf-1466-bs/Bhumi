import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { TimerPipe } from './pipes/timer.pipe';
import { SanitizeHtmlPipeModule } from './pipes/sanitize-html/sanitize-html.module';
import { SanitizeHtmlPipe } from './pipes/sanitize-html/sanitize-html.pipe';
import { ExtractHtmlTextPipeModule } from './pipes/extract-html-text/extract-html-text.module';
import { ExtractHtmlTextPipe } from './pipes/extract-html-text/extract-html-text.pipe';

@NgModule({
    declarations: [TimerPipe],
    imports: [
        CommonModule, 
        FormsModule, 
        ReactiveFormsModule, 
        TranslateModule, 
        SanitizeHtmlPipeModule,
        ExtractHtmlTextPipeModule
    ],
    exports: [
        CommonModule,
        FormsModule,
        TimerPipe,
        ReactiveFormsModule,
        TranslateModule,
        SanitizeHtmlPipe,
        ExtractHtmlTextPipe
    ],
})
export class SharedModule {}
