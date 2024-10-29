import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RetrieveActionComponent } from './retrieve-action.component';

describe('RetrieveActionComponent', () => {
  let component: RetrieveActionComponent;
  let fixture: ComponentFixture<RetrieveActionComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ RetrieveActionComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RetrieveActionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
function beforeEach(arg0: () => Promise<void>) {
  throw new Error('Function not implemented.');
}

function expect(component: RetrieveActionComponent) {
  throw new Error('Function not implemented.');
}

