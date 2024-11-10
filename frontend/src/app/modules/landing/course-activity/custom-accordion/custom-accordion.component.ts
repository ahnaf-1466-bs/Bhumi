import { CdkAccordionItem } from '@angular/cdk/accordion';
import { Component, ElementRef, Input, OnChanges, SimpleChanges, ViewChild } from '@angular/core';

@Component({
  selector: 'custom-accordion',
  templateUrl: './custom-accordion.component.html',
  styleUrls: ['./custom-accordion.component.scss']
})
export class CustomAccordionComponent implements OnChanges{
  @ViewChild('accordionItem') accordionItem: CdkAccordionItem;
  @Input() headerText: string = 'Header';
  @Input() isOpened: boolean = false;
  @Input() isTopicCompleted: boolean = false;

  constructor(private elementRef: ElementRef){

  }

  // opens or collapses accordion based on changing values of isOpened
  ngOnChanges(changes: SimpleChanges): void {
    if (this.isOpened) {
      this.accordionItem?.open();
    }
    else{
      this.accordionItem?.close();
    }

    if(this.headerText)
    {
      this.headerText = this.extractTextFromHtml(this.headerText);
    }
  }

  extractTextFromHtml(htmlString: string): string {
    const tempElement = this.elementRef.nativeElement.cloneNode();
    tempElement.innerHTML = htmlString;
    return tempElement.textContent || tempElement.innerText || '';
  }
  
}
