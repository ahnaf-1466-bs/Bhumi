import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PdfActivityComponent } from './pdf-activity.component';

describe('PdfActivityComponent', () => {
  let component: PdfActivityComponent;
  let fixture: ComponentFixture<PdfActivityComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PdfActivityComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PdfActivityComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
