import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WhyVumiComponent } from './why-vumi.component';

describe('WhyVumiComponent', () => {
  let component: WhyVumiComponent;
  let fixture: ComponentFixture<WhyVumiComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ WhyVumiComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WhyVumiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
