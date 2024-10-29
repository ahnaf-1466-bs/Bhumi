import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OurStrengthsComponent } from './our-strengths.component';

describe('OurStrengthsComponent', () => {
  let component: OurStrengthsComponent;
  let fixture: ComponentFixture<OurStrengthsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ OurStrengthsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OurStrengthsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
