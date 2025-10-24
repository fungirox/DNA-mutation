import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { DnaChecker } from "./components/dna-checker/dna-checker";


@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, DnaChecker],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('dna-mutation-client');
  


}
 