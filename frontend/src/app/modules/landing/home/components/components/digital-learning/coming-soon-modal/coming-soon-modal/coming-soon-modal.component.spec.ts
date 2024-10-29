import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ComingSoonModalComponent } from './coming-soon-modal.component';

describe('ComingSoonModalComponent', () => {
  let component: ComingSoonModalComponent;
  let fixture: ComponentFixture<ComingSoonModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ComingSoonModalComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ComingSoonModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
