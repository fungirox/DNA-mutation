import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Api } from '../../services/api';
import { response } from 'express';

@Component({
  selector: 'app-dna-checker',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './dna-checker.html',
  styleUrl: './dna-checker.css'
})
export class DnaChecker implements OnInit {
  constructor(private api: Api) { }

  input: any = null;
  statsGraph: any; 
  isActive: any;
  
  // Variables de consulta para almacenar los datos de la API y mostrar en la vista
  result: any = null;
  stats: any = null;
  list: any = null;

  // Este método carga al iniciar la página
  ngOnInit(): void {
    this.loadStats();
    this.loadList();
    this.graphStats();
  }

  isMutant() {
    console.log(this.input);
    
    this.result = false;
  }

  // Consulta usando el servicio de API a nuestra API y almacena los resultados obtenidos o imprime un error en la consola.
  loadStats(): void {
    this.api.getStats().subscribe({
      next: (response) => {
        this.stats = response;
      },
      error: (error) => {
        console.error('Error al obtener las stats:', error);
      }
    });
  }

  // Igual que el método anterior consulta en la API y regresa dichos resultados o imprime un error en la consulta.
  loadList(): void {
    this.api.getList().subscribe({
      next: (response) => {
        this.list = response;
      },
      error: (error) => {
        console.error('Error al obtener la lista:', error);
      }

    });
  }

  graphStats(){
    this.statsGraph = Array(10);
    this.isActive = this.stats.rate * 10;
    // this.statsGraph.forEach(element => {
      
    // });
  }
  
}
