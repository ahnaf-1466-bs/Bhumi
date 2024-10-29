import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SyllabusSingleComponent } from './syllabus-single.component';

describe('SyllabusSingleComponent', () => {
  let component: SyllabusSingleComponent;
  let fixture: ComponentFixture<SyllabusSingleComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SyllabusSingleComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SyllabusSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
