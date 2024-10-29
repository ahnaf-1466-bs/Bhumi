import { ElementRef, Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'extractHtmlText'
})
export class ExtractHtmlTextPipe implements PipeTransform {
  constructor(private elementRef: ElementRef){}

  transform(htmlString: string): string {
    const tempElement = this.elementRef.nativeElement.cloneNode();
    tempElement.innerHTML = htmlString;
    return tempElement.textContent || tempElement.innerText || '';
  }
}
