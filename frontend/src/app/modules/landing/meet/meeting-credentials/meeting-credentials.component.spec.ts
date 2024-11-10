import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MeetingCredentialsComponent } from './meeting-credentials.component';

describe('MeetingCredentialsComponent', () => {
  let component: MeetingCredentialsComponent;
  let fixture: ComponentFixture<MeetingCredentialsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MeetingCredentialsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MeetingCredentialsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
