import { Component, Input } from '@angular/core';
import { Operation } from '../../models/operation';


@Component({
  selector: 'app-operation',
  templateUrl: './operation.component.html',
  styleUrls: ['./operation.component.scss']
})
export class OperationComponent {
  @Input() operationList:Operation[];
  @Input() mxTierOperation:number;

  firstTier: Operation[] = [];
  tier: Operation[][] = [];
  rows:number = 0;
  isBengali: boolean = false;

  ngOnInit(): void {
    if (localStorage.getItem('lang') === 'bn') {
        this.isBengali = true;
    } else {
        this.isBengali = false;
    }
  }

  ngOnChanges(){

      for(let opt of this.operationList){
           if(!opt.operationname_bn){   
               opt.operationname_bn = opt.operationname;
           }
           if(!opt.operationdeg_bn){   
               opt.operationdeg_bn = opt.operationdeg;
           } 
      }
     
      this.rows = this.mxTierOperation;

      for(let operation of this.operationList) {
          if(operation.tier == 1){
              this.firstTier.push(operation);
          }
      }
      
      for(let i = 0; i < this.rows; i++) {
            this.tier[i] = [];
            for(let operation of this.operationList) {
                if(operation.tier == i+2)this.tier[i].push( operation );
            }
      }  
            
  }


  public getNumberArray(length: number): number[] {
      return Array(length).fill(0).map((x, i) => i + 1);
  }

}
