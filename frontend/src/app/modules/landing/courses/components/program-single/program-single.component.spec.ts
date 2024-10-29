import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProgramSingleComponent } from './program-single.component';

describe('ProgramSingleComponent', () => {
  let component: ProgramSingleComponent;
  let fixture: ComponentFixture<ProgramSingleComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ProgramSingleComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ProgramSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
