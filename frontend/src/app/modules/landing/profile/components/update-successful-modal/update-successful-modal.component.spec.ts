import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateSuccessfulModalComponent } from './update-successful-modal.component';

describe('UpdateSuccessfulModalComponent', () => {
  let component: UpdateSuccessfulModalComponent;
  let fixture: ComponentFixture<UpdateSuccessfulModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UpdateSuccessfulModalComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UpdateSuccessfulModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
