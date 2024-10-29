import { DOCUMENT, DatePipe } from '@angular/common';
import { Component, Inject, OnInit, ViewEncapsulation } from '@angular/core';
import {
    DomSanitizer,
    Meta,
    SafeResourceUrl,
    Title,
} from '@angular/platform-browser';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { AllNewsfeedApiService } from './services/all-newsfeed.service';
import { NewsfeedByIdApiService } from './services/newsfeed-by-id.service';

@Component({
    selector: 'app-newsfeeds',
    templateUrl: './newsfeeds.component.html',
    styleUrls: ['./newsfeeds.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class NewsfeedsComponent implements OnInit {
    constructor(
        private meta: Meta,
        private route: ActivatedRoute,
        private sanitizer: DomSanitizer,
        private titleForThumbnail: Title,
        private _allNews: AllNewsfeedApiService,
        private _newsById: NewsfeedByIdApiService,
        @Inject(DOCUMENT) private document: Document
    ) {}

    id;
    title;
    title_bn;
    picture;
    newsBody;
    newsBody_bn;
    publishDate;
    formattedDate;
    slicedDescription;
    descriptionElement;
    isBengali: boolean = false;

    ngOnInit() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;

        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }

        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
            this._allNews.getAllNewsFeedData().subscribe((res: any) => {});
            this._newsById.getNewsFeedById(this.id).subscribe((res: any) => {
                this.picture = res?.picurl;
                this.meta.addTag({
                    name: 'og:image',
                    content: this.picture,
                });
                this.title = res?.newstitle;
                this.title_bn = res?.newstitle_bn;
                this.titleForThumbnail.setTitle(this.title);
                this.newsBody = res?.newsbody;
                this.newsBody_bn = res?.newsbody_bn;
                this.slicedDescription = res?.newsbody.slice(0, 200);

                // Update the innerHTML of the description-container
                this.descriptionElement = document.getElementById(
                    'description-container'
                );
                if (this.descriptionElement) {
                    this.descriptionElement.innerHTML = this.slicedDescription;
                } else {
                    console.error('descriptionElement not found!');
                }

                var desiredText = '';
                if (this.descriptionElement) {
                    var paragraphs =
                        this.descriptionElement.getElementsByTagName('p');
                    if (paragraphs.length > 1) {
                        desiredText =
                            paragraphs[1].textContent.trim() +
                            paragraphs[2].textContent.trim();
                    }
                }

                this.meta.updateTag({
                    name: 'description',
                    content: desiredText,
                });

                // Set fixed height and width for og:image
                const imageWidth = '120px';
                const imageHeight = '100px';
                const imageStyle = `min-width: ${imageWidth}; min-height: ${imageHeight};`;

                // Sanitize the image URL
                const safeImageUrl: SafeResourceUrl =
                    this.sanitizer.bypassSecurityTrustResourceUrl(this.picture);

                // Add the image meta tag with class and style attributes
                this.meta.updateTag({
                    name: 'og:image',
                    content: safeImageUrl.toString(),
                    class: 'min-h-fit',
                    style: imageStyle,
                });

                this.publishDate = res.dateofpublish;
                const datePipe = new DatePipe('en-US');
                this.formattedDate = datePipe.transform(
                    this.publishDate,
                    'yyyy-MM-dd'
                );
            });
        });
    }
}
