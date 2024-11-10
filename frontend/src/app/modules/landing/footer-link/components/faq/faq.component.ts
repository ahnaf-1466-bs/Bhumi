import { Component, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FooterLinkApiService } from '../../services/footer-link-api.service';
import { DOCUMENT } from '@angular/common';

@Component({
  selector: 'app-faq',
  templateUrl: './faq.component.html',
  styleUrls: ['./faq.component.scss']
})
export class FaqComponent {
  name:string = "";
  loading:boolean = true;
  questions:any[] = [];
  questions_bn:any[] = [];
  answers:any[] = [];
  answers_bn:any[] = [];
  isOpen: boolean[] = [];
  activeIndex: number = null;
  isBengali: boolean = false;

  constructor(
    private route: ActivatedRoute,
    private footerDataApi: FooterLinkApiService,
    @Inject(DOCUMENT) private document: Document
  ){}

  ngOnInit(){

    this.document.body.scrollTop = 0;
    this.document.documentElement.scrollTop = 0;

    if (localStorage.getItem('lang') === 'bn') {
        this.isBengali = true;
    }
    else{
        this.isBengali = false;
    }


    this.footerDataApi.getFootLinkData().subscribe((res: any) => {
        
        if (res.links) {
          this.loading = false;
      
          const faqLinks = res.links.filter((link: any) => link.name === "faq");
         
          this.questions = faqLinks.map((link: any) => link.title);
          this.questions_bn = faqLinks.map((link: any) => link.title_bn);

          this.answers = faqLinks.map((link: any) => link.description);
          this.answers_bn = faqLinks.map((link: any) => link.description_bn);
      
        }
    });
    
    
  }

  toggleAccordion(index: number) {
    if (this.activeIndex === index) {
      this.activeIndex = null;
    } else {
      this.activeIndex = index;
    }
  }
  

}
