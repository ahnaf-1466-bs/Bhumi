import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AboutPageApiService } from '../../services/about-page-api.service';

@Component({
    selector: 'app-about',
    templateUrl: './about.component.html',
    styleUrls: ['./about.component.scss'],
})
export class AboutComponent {
    constructor(
        private aboutApi: AboutPageApiService,
        private _router: Router
    ) {}

    loading: boolean;
    ourStory: string;
    ourStory_bn: string;
    vision: string;
    vision_bn: string;
    whyVumi: string;
    whyVumi_bn: string;
    forWhom: any[];
    ourStrengths: any[];

    ngOnInit() {
        this.loading = true;
        this.aboutApi.getAboutPageData().subscribe((res: any) => {
            this.loading = false;

            if (res.ourstory) {
                this.ourStory = res?.ourstory;
                this.ourStory_bn = res?.ourstory_bn;
            }

            if (res.vision) {
                this.vision = res?.vision;
                this.vision_bn = res?.vision_bn;
            }

            if (res.whyvumi) {
                this.whyVumi = res?.whyvumi;
                this.whyVumi_bn = res?.whyvumi_bn;
            }

            if (res.whoisvumifor) {
                this.forWhom = res?.whoisvumifor;
            }

            if (res.ourstrength) {
                this.ourStrengths = res?.ourstrength;
            }
        });
    }
}
