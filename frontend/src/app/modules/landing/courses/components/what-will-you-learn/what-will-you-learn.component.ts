import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-what-will-you-learn',
    templateUrl: './what-will-you-learn.component.html',
    styleUrls: ['./what-will-you-learn.component.scss'],
})
export class WhatWillYouLearnComponent {
    @Input() mobile: boolean;
    @Input() dataFromParent: any;

    constructor(private translateService:TranslateService) {}

    learn!:any;

    ngOnChanges() {
        if(this.dataFromParent?.learn?.length>0)
        {
            this.learn = this.dataFromParent?.learn[0];

            if(this.translateService.getDefaultLang()==='bn')
            {
                this.learn.learning=this.learn.learning_bangla;
            }
        }
    }
}
