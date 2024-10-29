import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'app-course-single-feature',
  templateUrl: './course-single-feature.component.html',
  styleUrls: ['./course-single-feature.component.scss']
})
export class CourseSingleFeatureComponent {
  @Input() imageURL: string = "";
  @Input() english_text: string = "";
  @Input() bangla_text:string="";
  lang!:string;
  constructor(private translateService:TranslateService)
  {

  }

  ngOnInit()
  {
    this.lang = this.translateService.getDefaultLang();
  }
}
