import { DOCUMENT } from '@angular/common';
import { Component, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FooterLink } from '../../models/footer-link';
import { FooterLinkApiService } from '../../services/footer-link-api.service';

@Component({
    selector: 'app-footer-link',
    templateUrl: './footer-link.component.html',
    styleUrls: ['./footer-link.component.scss'],
})
export class FooterLinkComponent {
    name: string = '';
    data: FooterLink = {} as FooterLink;
    loading: boolean = true;
    isBengali: boolean = false;
    questions: any[] = [];
    answers: any[] = [];

    constructor(
        private route: ActivatedRoute,
        private footerDataApi: FooterLinkApiService,
        @Inject(DOCUMENT) private document: Document
    ) {}

    ngOnInit() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;
        this.checkLanguage();

        this.route.queryParams.subscribe((params) => {
            this.loading = true;
            this.name = params.name;

            this.footerDataApi.getFootLinkData().subscribe((res: any) => {
                this.loading = false;
                this.checkLanguage();

                if (res.links) {
                    this.loading = false;

                    for (let fData of res.links) {
                        if (fData.name == this.name) {
                            this.makeHilight(fData);
                            this.data = fData;
                        }
                    }
                }
            });
        });
    }

    checkLanguage() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }

    makeHilight(data: any) {
        data.description = data.description.replace(/<h1>/g, '<strong>');
        data.description = data.description.replace(/<\/h1>/g, '</strong>');

        data.description = data.description.replace(/<h2>/g, '<strong>');
        data.description = data.description.replace(/<\/h2>/g, '</strong>');

        data.description = data.description.replace(/<h3>/g, '<strong>');
        data.description = data.description.replace(/<\/h3>/g, '</strong>');

        data.description = data.description.replace(/<h4>/g, '<strong>');
        data.description = data.description.replace(/<\/h4>/g, '</strong>');

        data.description = data.description.replace(/<h5>/g, '<strong>');
        data.description = data.description.replace(/<\/h5>/g, '</strong>');

        data.description = data.description.replace(/<h6>/g, '<strong>');
        data.description = data.description.replace(/<\/h6>/g, '</strong>');
    }
}
