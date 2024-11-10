import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WhoWillBeBenefitedComponent } from './who-will-be-benefited.component';

describe('WhoWillBeBenefitedComponent', () => {
  let component: WhoWillBeBenefitedComponent;
  let fixture: ComponentFixture<WhoWillBeBenefitedComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ WhoWillBeBenefitedComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WhoWillBeBenefitedComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
