<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\ConfigCalculadora;
use App\Models\ConfigContraOferta;
use App\Models\Basica;
use App\Models\Country;
use App\Models\Filtrado;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Variables;
use App\Models\Atributos;
use App\Models\Parascore;
use App\Models\ContraOferta;
use App\Models\Correos;
use App\Models\Evaluacion;
use App\Models\desembolso;
use App\Models\Pagos;
use App\Models\Repagos;
use App\Models\PagosParciales;
use App\Models\CodigosValidaciones;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use DateTime;
use Carbon\Carbon;


class AnalisisCreditosIncompletos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analisis:incompleto';
        private $NAME_CONTROLLER = 'envio cron analisis incompleto';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Acualizar creditos a usuarios con creditos en estatus de incompleto';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //


         try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $creditos = Calculadora::
            join('users', 'users.id', '=', 'calculadoras.idUserFk')
            ->join('basicas', 'users.id', '=', 'basicas.idUserFk')
            ->join('financieras', 'users.id', '=', 'financieras.idUserFk')
            ->join('referencias', 'users.id', '=', 'referencias.idUserFk')
            ->select('calculadoras.id as id_solicitud','users.*','calculadoras.*')
            ->where('calculadoras.estatus','incompleto')
            ->get();
            $fecha_actual = date("Y-m-d H:i:s");
            $contenido="";
                foreach($creditos as $credito){
                    $id = $credito->idUserFk;
                    $idSolicitud = 0;
                    if(Basica::where('idUserFk',$id)->exists() && Financiera::where('idUserFk',$id)->exists() && Referencias::where('idUserFk',$id)->exists()){

                    $usuario=User::where('id',$id)->first();
                    $basica=Basica::where('idUserfk',$id)->first();
                    $referencia=Referencias::where('idUserfk',$id)->first();
                    $financiera=Financiera::where('idUserfk',$id)->first();
                    $atributos=Atributos::all();
                    $variables=Variables::all();
                    $scoreNegado=Parascore::where('caso','negado')->first();
                    $scoreAprovado=Parascore::where('caso','aprobado')->first();
                    $scorePreaprovado=Parascore::where('caso','preaprobado')->first();
                    $balance_inicial = DB::table('variables')->where('status',0)->sum('puntosTotales');
                    $estatus_solicitud ='aprobado';
                    
                    $suma_basica=0;
                 if($basica->genero=='Masculino'){
                     $atributo=Atributos::where('variable', 'Genero')->where('categoria','Masculino')->first();
                     $variable= Variables::where('variable', 'Genero')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                    //  var_dump(floatval((($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0)));
                 }else if($basica->genero=='Femenino'){
                    $atributo=Atributos::where('variable', 'Genero')->where('categoria','Femenino')->first();
                    $variable= Variables::where('variable', 'Genero')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                // $fecha_nacimiento=$basica->fechaNacimiento;
                //  $edad=time()-time($fecha_nacimiento);
                if($basica->fechaNacimiento != "Invalid date"){
                    $edad = Carbon::parse($basica->fechaNacimiento)->age; 
                 }else{
                     $edad =0;
                 }
                if($edad<=23){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Jóven')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                } else if($edad>23 && $edad<=32){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Adulto Jóven')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($edad>32 && $edad<=50){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Adulto Maduro')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($edad>50 && $edad<=59){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Adulto Mayor')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($edad>59 ){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Senior')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                $ciudad=Country::where('name',$basica->ciudad)->first();

                if($ciudad->zonaGeografica == 'AMAZONIA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','AMAZONIA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'ANDINA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','ANDINA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                }else if($ciudad->zonaGeografica == 'ORINOQUIA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','ORINOQUIA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'CARIBE'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','CARIBE')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'BOGOTA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','BOGOTA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'PACIFICA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','PACIFICA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                // if(!empty($usuario->phone_number)){
                //     $atributo=Atributos::where('variable','Teléfono 1')->where('categoria','Celular personal')->first();
                //     $variable= Variables::where('variable', 'Teléfono 1')->first();
                //      $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                      
                     
                // }
                
                if($basica->tipoVivienda == 'Rentada'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Rentada')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->tipoVivienda == 'Propia'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Propia')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                     
                }else if($basica->tipoVivienda == 'Familiar'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Familiar')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->tipoVivienda == 'Hipotecada'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Hipotecada')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }

                if($basica->tienmpoVivienda == 'Menos de 1 año'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','Menos de 1 año')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '1 a 2 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','1 a 2 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                }else if($basica->tienmpoVivienda == '2 a 4 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','2 a 4 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '4 a 5 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','4 a 5 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == 'Más de 5 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','Más de 5 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '2 meses'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','2 meses')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '4 meses'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','4 meses')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '6 meses'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','6 meses')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->estrato == '1'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 1')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->estrato == '2'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 2')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->estrato == '3'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 3')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->estrato == '4'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 4')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                     
                }else if($basica->estrato == '5'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 5')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->estrato == '6'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 6')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }

                if($basica->estadoCivil == 'Soltero'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Soltero/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                      
                }else if($basica->estadoCivil == 'Casado'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Casado/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->estadoCivil == 'Unión Libre'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','En unión libre')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->estadoCivil == 'Divorciado'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Divorciado/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->estadoCivil == 'Viudo'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Viudo/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }

                if($basica->personasaCargo == 'Ninguna'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','Ninguna')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);   
                }else if($basica->personasaCargo == 'Una Persona'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','1 Persona')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                     
                }else if($basica->personasaCargo == 'Dos Personas'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','2 Personas')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);   
                }else if($basica->personasaCargo == 'Tres Personas'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','3 Personas')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);   
                }else if($basica->personasaCargo == 'Mas de Tres Personas'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','Mas de 3 personas')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);   
                }

                if($basica->conquienVives == 'Solo'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Solo')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->conquienVives == 'Padres y/o Hermanos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Padres y/o hermanos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                     
                }else if($basica->conquienVives == 'Esposo(a) y/o Pareja'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Esposo/a o pareja')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->conquienVives == 'Esposo(a) y/o Pareja con hijos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Esposo/a o pareja con hijos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->conquienVives == 'Unicamente Hijos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Únicamente hijos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->conquienVives == 'Amigos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Amigos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->conquienVives == 'Otro'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Otro')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }

                if($basica->tipoPlanMovil == 'Pospago'){
                    $atributo=Atributos::where('variable','Tipo de plan móvil')->where('categoria','Pospago')->first();
                    $variable= Variables::where('variable', 'Tipo de plan móvil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if($basica->tipoPlanMovil == 'Prepago'){
                    $atributo=Atributos::where('variable','Tipo de plan móvil')->where('categoria','Prepago')->first();
                    $variable= Variables::where('variable', 'Tipo de plan móvil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                     
                }

                if($basica->nivelEstudio == 'Ninguno'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Ninguno')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Básico' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Básico en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Básico' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Básico finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                       
                }else if($basica->nivelEstudio == 'Bachiller' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Bachiller en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Bachiller' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Bachiller finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Técnico' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Técnico en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Técnico' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Técnico finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Tecnólogo' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Tecnólogo en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Tecnólogo' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Tecnólogo finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Pregrado' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Pregrado en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Pregrado' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Pregrado finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Postgrado' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Posgrado en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->nivelEstudio == 'Postgrado' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Posgrado finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }

                if($basica->vehiculo == 'Ninguno'){
                    $atributo=Atributos::where('variable','¿Tienes vehículo propio?')->where('categoria','Ninguno')->first();
                    $variable= Variables::where('variable', '¿Tienes vehículo propio?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->vehiculo == 'Carro'){
                    $atributo=Atributos::where('variable','¿Tienes vehículo propio?')->where('categoria','Carro')->first();
                    $variable= Variables::where('variable', '¿Tienes vehículo propio?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->vehiculo == 'Moto'){
                    $atributo=Atributos::where('variable','¿Tienes vehículo propio?')->where('categoria','Moto')->first();
                    $variable= Variables::where('variable', '¿Tienes vehículo propio?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }

                if($basica->centralRiesgo == 'Si'){
                    $atributo=Atributos::where('variable','¿Reportado en data crédito?')->where('categoria','Si')->first();
                    $variable= Variables::where('variable', '¿Reportado en data crédito?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if($basica->centralRiesgo == 'No'){
                    $atributo=Atributos::where('variable','¿Reportado en data crédito?')->where('categoria','No')->first();
                    $variable= Variables::where('variable', '¿Reportado en data crédito?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }

                if(trim($financiera->situacionLaboral)=='Empleado/a termino indefinido'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a termino indefinido')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else if(trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a termino fijo renovable')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if(trim($financiera->situacionLaboral)=='Empleado/a por servicios'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a por servicios')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if(trim($financiera->situacionLaboral)=='Empleado/a obra labor'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a obra labor')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if(trim($financiera->situacionLaboral)=='Empleado/a temporal'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a temporal')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if(trim($financiera->situacionLaboral)=='Independiente'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Independiente')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if(trim($financiera->situacionLaboral)=='Estudiante'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Estudiante')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }else if(trim($financiera->situacionLaboral)=='Pensionado'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Pensionado')->first();
                    
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0); 
                }


                if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Administrativas (jefes, coordinadores, asistentes, auxiliar y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Administrativas (jefes.coordinadores.asistentes.auxiliares y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Gerenciales (gerentes, subgerentes, directores, y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Gerenciales (gerentes.subgerentes.directores y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Operativas y de servicio (supervisores, maquinistas, operarios, analistas y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Operativas y de servicio(supervisores.maquinistas.operarios.analistas y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                       
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Ventas y Mercadeo (vendedores, agentes y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Ventas y mercadeo(vendedores.agentes y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Técnicas (ingeniería, investigación y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Otro')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Vigilancia y seguridad'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Vigilancia y seguridad')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Taxista'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Taxista')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Conductor/transportador'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Conductor/transportador')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Secretariales'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Secretariales')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Areas de apoyo (sistemas, contabilidad, auditoria, revisoria y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Áreas de apoyo(sistemas.contabilidad.auditoria.revosoria y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Policia'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Policía')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Militar'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Militar')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Cajero'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Cajero')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Otro'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Otro')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }





                if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Abogado'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Abogado ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'actividades Agropecuarias'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Actividades agropecuarias')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Ama de casa'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Ama de casa')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                       
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Artistas'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Artistas ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Cocineros'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Cocineros')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Contador/Revisor'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Contador/revisor ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Empresario pyme'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Empresario pyme')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Ingeniero/Geologo/Arquitectos'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','ingeniero/geologo/arquitecto')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Medico/Profesionales del sector salud'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Medico/profesional sector salud')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Mensajero'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Mensajero ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Microempresario'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Microempresario ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Operarios de maquinaria'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Operarios de maquinaria')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Peluquero'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Peluquero')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);  
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Plomero/Albañil'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Plomero/albañil')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Profesional o tecnico en informatica (software)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Profesional o tecnico en informatica (software)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Profesores/Docentes'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Profesores/docentes')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Secretarias'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Secretarias ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Servicios de vigilancia/Policias/Militares'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Servicios de vigilancia/Policias/militares')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Taxista/Transportador/Conductor'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Taxistas/ transportador/conductor')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Tecnicos o tecnologos en otras areas'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Tecnicos o tecnologos en otras areas ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Vendedores/Visitadores'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Vendedores/visitadores')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Otro'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','otros ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Estetica'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Estetica ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($financiera->situacionLaboral == 'Desempleado' || $financiera->situacionLaboral=='Estudiante'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Desempleado')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($financiera->situacionLaboral == 'Pensionado'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Jubilado / Pensionado')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($financiera->antiguedadLaboral == 'menos de 2 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','Menos de 2 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '3 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','3 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '4 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','4 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '5 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','5 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '6 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','6 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '7 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','7 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '8 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','8 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '9 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','9 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }
                else
                if($financiera->antiguedadLaboral == '10 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','10 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '11 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','11 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == '1 Año'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','1 año')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == 'Dos Años'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','2 años')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else
                if($financiera->antiguedadLaboral == 'Tres Años o más'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','3 años o mas')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }
                

                if($financiera->ingresoTotalMensual <= 684600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','1-$684,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 684600 && $financiera->ingresoTotalMensual <= 877800){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$684,600-$877,800')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 877800 && $financiera->ingresoTotalMensual <= 1040600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$877,800-$1,040,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 1040600 && $financiera->ingresoTotalMensual <= 1233600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,040,600-$1,233,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 1233600 && $financiera->ingresoTotalMensual <= 1462400){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,233,600-$1,462,400')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 1462400 && $financiera->ingresoTotalMensual <= 1733600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,462,400-$1,733,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 1733600 && $financiera->ingresoTotalMensual <= 2055100){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,733,600-$2,055,100')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 2055100 && $financiera->ingresoTotalMensual <= 2436200){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$2,055,100-$2,436,200')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 2436200 && $financiera->ingresoTotalMensual <= 2888000){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$2,436,200-$2,888,000')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 2888000 && $financiera->ingresoTotalMensual <= 3423600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$2,888,000-$3,423,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->ingresoTotalMensual > 3423600 && $financiera->ingresoTotalMensual <= 99999999){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$3,423,600-$99,999,999')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }


                if($financiera->otroIngreso =='Si'){

                    if($financiera->proviene =='Arriendo'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Rentas')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                    }else if($financiera->proviene =='Salario Pareja'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Salario pareja')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                    }else if($financiera->proviene =='Actividad Comercial Extra'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Actividad comercial extra')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                    }else if($financiera->proviene =='Otro'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Otro')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                    }

                    
                  $totalotroIngreso=$financiera->total_otro_ingr_mensual;
                 
                }else{
                    $totalotroIngreso=0; 
                }

                if(!empty($financiera->total_otro_ingr_mensual)){
                    if($financiera->tipoCuenta =='Ahorros'){
                        $atributo=Atributos::where('variable','Tipo de Cuenta bancaria 1')->where('categoria','Cuenta de ahorro')->first();
                        $variable= Variables::where('variable', 'Tipo de Cuenta bancaria 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                    }else if($financiera->tipoCuenta =='Corriente'){
                        $atributo=Atributos::where('variable','Tipo de Cuenta bancaria 1')->where('categoria','Cuenta Corriente')->first();
                        $variable= Variables::where('variable', 'Tipo de Cuenta bancaria 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                     
                    }  
                }

                if(!empty($referencia->ReferenciaPersonalNombres)){
                    $atributo=Atributos::where('variable','Referencia 1 - Nombre:')->where('categoria','(Todas las categorías)')->first();
                    $variable= Variables::where('variable', 'Referencia 1 - Nombre:')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if(!empty($referencia->ReferenciaFamiliarNombres)){
                    $atributo=Atributos::where('variable','Referencia 2 - Nombre:')->where('categoria','(Todas las categorías)')->first();
                    $variable= Variables::where('variable', 'Referencia 2 - Nombre:')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($basica->cotizasSeguridadSocial == "Si"){
                    $atributo=Atributos::where('variable','¿Cotizas a  Seguridad Social?')->where('categoria','Si')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->cotizasSeguridadSocial == "No" ){
                    $atributo=Atributos::where('variable','¿Cotizas a  Seguridad Social?')->where('categoria','No')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($basica->entidadReportado == "Bancos o entidades de financiamiento"){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Bancos o entidades de financiamiento')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->entidadReportado == "Empresa de Telecomunicaciones" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Empresa de Telecomunicaciones')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->entidadReportado == "Otros" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Otros')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($basica->estadoReportado == "castigada"){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Castigada')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->estadoReportado == "deuda pagada" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','deuda paga')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->estadoReportado == "reestructurada" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Reestructurada')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->estadoReportado == "no paga" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','No paga')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($basica->tiempoReportado == "1 mes"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','1 mes')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "2 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','2 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "3 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','3 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "4 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','4 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "5 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','5 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "6 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','6 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "7 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','7 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "8 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','8 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "9 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','9 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "10 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','10 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "11 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','11 meses')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($basica->tiempoReportado == "mas de 1 ano"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','Mas de 1 año')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($financiera->creditosBanco == "Si"){
                    $atributo=Atributos::where('variable','Tienes o has tenido una obligación (crédito) con una entidad financiera ?')->where('categoria','Si')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->creditosBanco == "No"){
                    $atributo=Atributos::where('variable','Tienes o has tenido una obligación (crédito) con una entidad financiera ?')->where('categoria','No')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($financiera->otrasCuentas == "Si"){
                    $atributo=Atributos::where('variable','Tienes otras cuentas de ahorro o corriente?')->where('categoria','Si')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->otrasCuentas == "No"){
                    $atributo=Atributos::where('variable','Tienes otras cuentas de ahorro o corriente?')->where('categoria','No')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if($financiera->tarjetasCredito == "Si"){
                    $atributo=Atributos::where('variable','Tienes tarjeta de credito')->where('categoria','Si')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if($financiera->tarjetasCredito == "No"){
                    $atributo=Atributos::where('variable','Tienes tarjeta de credito')->where('categoria','No')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }

                if(trim($basica->tipoAfiliacion) == "Cotizante"){
                    $atributo=Atributos::where('variable','Tipo de afiliacion')->where('categoria','Cotizante')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }else if(trim($basica->tipoAfiliacion) == "Beneficiario"){
                    $atributo=Atributos::where('variable','Tipo de afiliacion')->where('categoria','Beneficiario')->first();
                    
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                 
                }
                    $solicitudCount=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$id)->count();
                    $solicitudAPCount=Calculadora::where('estatus', 'aprobado')->where('idUserFk',$id)->count();
                    
                    $total_total=$suma_basica*100/953;
                    if(round($total_total,2) >= $scoreNegado->desde && round($total_total,2) <= $scoreNegado->hasta){
                        // $estatus_solicitud = $scoreNegado->caso;
                    }else if(round($total_total,2) >= $scorePreaprovado->desde && round($total_total,2) <= $scorePreaprovado->hasta){
                        // $estatus_solicitud = $scorePreaprovado->caso;
                    }else if(round($total_total,2) >= $scoreAprovado->desde && round($total_total,2) <= $scoreAprovado->hasta){
                        // $estatus_solicitud = $scoreAprovado->caso;
                    }
                    $contra_oferta = false;
                    // verificacion de numero telefonico contra operadoras
                $usuarioTelefono = \DB::table('users')
                ->where('users.id',$id)
                ->select('users.phone_number as phone_number')
                ->first();
                if($usuarioTelefono == null){
                    //si el usuario no tiene telefono
                    $estatus_solicitud ='negado';
                    // $evaluacion_telefono = 'negado';
                }else{
                    if(strlen($usuarioTelefono->phone_number) == 10){
                        $prefijo = substr($usuarioTelefono->phone_number, 0, 3);
                        $telefonoCorrecto = \DB::table('operadoras')
                        ->where('operadoras.prefijo',$prefijo)
                        ->first();
                        if($telefonoCorrecto == null){
                            //Si el prefijo del telefono no esta entre los registrados
                            $estatus_solicitud ='negado';
                            // $evaluacion_telefono = 'negado';
                        }
                    }else{
                        //si el telefono mide menos de 10 caracteres
                        $estatus_solicitud ='negado';
                        // $evaluacion_telefono = 'negado';
                    }
                }

                if($financiera->tiempoDatacredito > 4){
                    $estatus_solicitud ='negado';
                    // $evaluacion_filtro = "negado";
                }
                    
                    if($solicitudCount > 0 ){
                        
                        if($idSolicitud > 0){
                            $solicitud=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$id)->find($idSolicitud);
                        }else{
                            $solicitud=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$id)->first();
                        }

                        
                    
                        $caso1=Filtrado::where('id',1)->first();
                        $caso2=Filtrado::where('id',2)->first();
                        $caso3=Filtrado::where('id',3)->first();
                        $caso4=Filtrado::where('id',5)->first();
                        $caso5=Filtrado::where('id',6)->first();
                        $caso6=Filtrado::where('id',7)->first();
                        $caso7=Filtrado::where('id',8)->first();
                        $caso8=Filtrado::where('id',9)->first();
                        $caso9=Filtrado::where('id',10)->first();
                        $caso14=Filtrado::where('id',14)->first();
                        $caso16=Filtrado::where('id',16)->first();
                        $caso17=Filtrado::where('id',17)->first();
                        // $caso11=Filtrado::where('id',12)->first();
                        $totalingreso=$financiera->ingresoTotalMensual-$financiera->egresoTotalMensual;
                        $c5 = 0;
                        $c6 = 0;
                        $c14=0;
                        $aL = 0;
                        $tr = 0;
                        if($caso5->valor == "menos de 2 meses"){
                            $c5 = 1;
                        }else if($caso5->valor == "3 meses"){
                            $c5 = 3;
                        }else if($caso5->valor == "4 meses"){
                            $c5 = 4;
                        }else if($caso5->valor == "5 meses"){
                            $c5 = 5;
                        }else if($caso5->valor == "6 meses"){
                            $c5 = 6;
                        }else if($caso5->valor == "7 meses"){
                            $c5 = 7;
                        }else if($caso5->valor == "8 meses"){
                            $c5 = 8;
                        }else if($caso5->valor == "9 meses"){
                            $c5 = 9;
                        }else if($caso5->valor == "10 meses"){
                            $c5 = 10;
                        }else if($caso5->valor == "11 meses"){
                            $c5 = 11;
                        }else if($caso5->valor == "12 meses"){
                            $c5 = 12;
                        }else if($caso5->valor == "1 Año"){
                            $c5 = 12;
                        }else if($caso5->valor == "2 Año"){
                            $c5 = 24;
                        }else if($caso5->valor == "Tres Años o más"){
                            $c5 = 48;
                        }

                        if($caso6->valor == "menos de 2 meses"){
                            $c6 = 1;
                        }else if($caso6->valor == "3 meses"){
                            $c6 = 3;
                        }else if($caso6->valor == "4 meses"){
                            $c6 = 4;
                        }else if($caso6->valor == "5 meses"){
                            $c6 = 5;
                        }else if($caso6->valor == "6 meses"){
                            $c6 = 6;
                        }else if($caso6->valor == "7 meses"){
                            $c6 = 7;
                        }else if($caso6->valor == "8 meses"){
                            $c6 = 8;
                        }else if($caso6->valor == "9 meses"){
                            $c6 = 9;
                        }else if($caso6->valor == "10 meses"){
                            $c6 = 10;
                        }else if($caso6->valor == "11 meses"){
                            $c6 = 11;
                        }else if($caso6->valor == "12 meses"){
                            $c6 = 12;
                        }else if($caso6->valor == "1 Año"){
                            $c6 = 12;
                        }else if($caso6->valor == "2 Año"){
                            $c6 = 24;
                        }else if($caso6->valor == "Tres Años o más"){
                            $c6 = 48;
                        }
                        
                        if($caso14->valor == "1 mes"){
                            $c14 = 1;
                        }else  if($caso14->valor == "2 meses"){
                            $c14 = 2;
                        }else if($caso14->valor == "3 meses"){
                            $c14 = 3;
                        }else if($caso14->valor == "4 meses"){
                            $c14 = 4;
                        }else if($caso14->valor == "5 meses"){
                            $c14 = 5;
                        }else if($caso14->valor == "6 meses"){
                            $c14 = 6;
                        }else if($caso14->valor == "7 meses"){
                            $c14 = 7;
                        }else if($caso14->valor == "8 meses"){
                            $c14 = 8;
                        }else if($caso14->valor == "9 meses"){
                            $c14 = 9;
                        }else if($caso14->valor == "10 meses"){
                            $c14 = 10;
                        }else if($caso14->valor == "11 meses"){
                            $c14 = 11;
                        }else if($caso14->valor == "12 meses"){
                            $c14 = 12;
                        }else if($caso14->valor == "mas de 1 ano"){
                            $c14 = 12;
                        }

                        if($basica->tiempoReportado == "1 mes"){
                            $tr = 1;
                        }else  if($basica->tiempoReportado == "2 meses"){
                            $tr = 2;
                        }else if($basica->tiempoReportado == "3 meses"){
                            $tr = 3;
                        }else if($basica->tiempoReportado == "4 meses"){
                            $tr = 4;
                        }else if($basica->tiempoReportado == "5 meses"){
                            $tr = 5;
                        }else if($basica->tiempoReportado == "6 meses"){
                            $tr = 6;
                        }else if($basica->tiempoReportado == "7 meses"){
                            $tr = 7;
                        }else if($basica->tiempoReportado == "8 meses"){
                            $tr = 8;
                        }else if($basica->tiempoReportado == "9 meses"){
                            $tr = 9;
                        }else if($basica->tiempoReportado == "10 meses"){
                            $tr = 10;
                        }else if($basica->tiempoReportado == "11 meses"){
                            $tr = 11;
                        }else if($basica->tiempoReportado == "12 meses"){
                            $tr = 12;
                        }else if($basica->tiempoReportado == "mas de 1 ano"){
                            $tr = 12;
                        }

                        if($financiera->antiguedadLaboral == "menos de 2 meses"){
                            $aL = 1;
                        }else if($financiera->antiguedadLaboral == "3 meses"){
                            $aL = 3;
                        }else if($financiera->antiguedadLaboral == "4 meses"){
                            $aL = 4;
                        }else if($financiera->antiguedadLaboral == "5 meses"){
                            $aL = 5;
                        }else if($financiera->antiguedadLaboral == "6 meses"){
                            $aL = 6;
                        }else if($financiera->antiguedadLaboral == "7 meses"){
                            $aL = 7;
                        }else if($financiera->antiguedadLaboral == "8 meses"){
                            $aL = 8;
                        }else if($financiera->antiguedadLaboral == "9 meses"){
                            $aL = 9;
                        }else if($financiera->antiguedadLaboral == "10 meses"){
                            $aL = 10;
                        }else if($financiera->antiguedadLaboral == "11 meses"){
                            $aL = 11;
                        }else if($financiera->antiguedadLaboral == "12 meses"){
                            $aL = 12;
                        }else if($financiera->antiguedadLaboral == "1 Año"){
                            $aL = 12;
                        }else if($financiera->antiguedadLaboral == "Dos Años"){
                            $aL = 24;
                        }else if($financiera->antiguedadLaboral == "Tres Años o más"){
                            $aL = 48;
                        }
                        
                        if(trim($caso1->signo)=='<'){
                    
                    if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                    trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                    trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                    trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual < $caso1->valor ){
                        
                            $estatus_solicitud ='negado'; 
                        
                    
                        }

                }else if(trim($caso1->signo)=='>'){

                
                    if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                    trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                    trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                    trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual>$caso1->valor ){

                        
                                        
                                            $estatus_solicitud ='negado'; 
                                        
                                    
                                        }

                }else if(trim($caso1->signo)=='<='){
            
                    if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                    trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                    trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                    trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual<=$caso1->valor ){

                        
                                        
                                            $estatus_solicitud ='negado'; 
                                        
                                    
                                        }
                }else if(trim($caso1->signo)=='>='){
                
                    if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                    trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                    trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                    trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual>=$caso1->valor ){

                        
                                        
                                            $estatus_solicitud ='negado'; 
                                        
                                    
                                        }
                } 
                if(trim($caso2->signo)=='<'){
                        if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && $financiera->ingresoTotalMensual < $caso2->valor ){
                                            
                                            
                                                $estatus_solicitud ='negado'; 
                                                
                                            }

                    }else if(trim($caso2->signo)=='>'){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && $financiera->ingresoTotalMensual > $caso2->valor ){
                                            
                                                $estatus_solicitud ='negado'; 
                                                
                                            }
                    }else if($caso2->signo=='<='){

                            if(($financiera->situacionLaboral == 'Independiente' || $financiera->situacionLaboral=='Empleado/a por servicios') && $financiera->ingresoTotalMensual <= $caso2->valor ){
                                            
                                                $estatus_solicitud ='negado'; 
                                                
                                            }
                    }else if(trim($caso2->signo)=='>='){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && $financiera->ingresoTotalMensual <= $caso2->valor ){
                                                                
                                                                    $estatus_solicitud ='negado'; 
                                                                    
                                                                }

                    }

                        if($caso3->valor==$basica->cotizasSeguridadSocial){

                        
                    
                            $estatus_solicitud ='negado'; 
                        
                        }else if(trim($basica->cotizasSeguridadSocial)=='Si' && trim($basica->tipoAfiliacion)=="Beneficiario"){
                            
                        
                            $estatus_solicitud ='negado'; 
                        
                            } 
                            
                            if($caso4->valor==$basica->estrato){

                                
                            
                            $estatus_solicitud ='negado'; 
                            
                        } 

                        if(trim($caso5->signo) =='>'){
                                if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                                trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                                trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                                trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL>$c5)){
                                
                                    
                                    $estatus_solicitud ='negado'; 
                                    
                                } 
                            }else if(trim($caso5->signo) =='<'){
                                if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                                trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                                trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                                trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL<$c5)){
                                    
                                    $estatus_solicitud ='negado'; 
                                    
                                } 
                            }else if(trim($caso5->signo) =='>='){
                                if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                                trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                                trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                                trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL>=$c5)){
                                
                                    $estatus_solicitud ='negado'; 
                                    
                                } 
                            }else if(trim($caso5->signo) =='<='){
                                if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                                trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                                trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                                trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL<=$c5)){
                                    
                                    $estatus_solicitud ='negado'; 
                                    
                                } 
                            }else if(trim($caso5->signo) =='=='){
                                if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                                trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                                trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                                trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL==$c5)){
                                    
                                    $estatus_solicitud ='negado'; 
                                    
                                } 
                            }
                            if(trim($caso6->signo) =='>'){
                                if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL>$c6)){

                            
                                    $estatus_solicitud ='negado';

                                }
                            }else if(trim($caso6->signo) =='<'){
                                if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL<$c6)){
                            
                                    $estatus_solicitud ='negado';

                                }
                            }else if(trim($caso6->signo) =='>='){
                                if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL>=$c6)){
                        
                                    $estatus_solicitud ='negado';

                                }
                            }else if(trim($caso6->signo) =='<='){
                                if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL<=$c6)){
                                
                                    $estatus_solicitud ='negado';

                                }
                            }else if(trim($caso6->signo) =='=='){
                                if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL==$c6)){
                            
                                    $estatus_solicitud ='negado';

                                }
                            }


                            if(trim($caso14->signo) =='>'){

                                        if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr>$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){
                                
                                    $estatus_solicitud ='negado';
                                    
                                }

                            }else if(trim($caso14->signo) =='<'){
                            

                                        if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr<$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){
                                
                                    $estatus_solicitud ='negado';
                                    
                                }

                            }else if(trim($caso14->signo) =='<='){
                        

                                        if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr<=$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){
                                
                                    $estatus_solicitud ='negado';
                                    
                                }

                            }else if(trim($caso14->signo) =='>='){

                                

                                        if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr>=$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){
                                
                                    $estatus_solicitud ='negado';
                                    
                                }

                            }


                        
                            if(!empty($caso8->valor) && $caso8->valor != ""){
                                $explode = explode("|", $caso8->valor);
                                foreach ($explode as $key => $value) {
                                   if(trim($financiera->situacionLaboral) == trim($value)){
                                        $estatus_solicitud ='negado'; 
                                        // $evaluacion_filtro = "negado";
                                        // $evaluacion_caso8 = "negado";
                                        break;
                                    }  
                                }
                                
                            }  

                        

                            if(!empty($caso16->valor) && $caso16->valor != ""){
                                $explodeusoCredito = explode("|", $caso16->valor);
                                foreach ($explodeusoCredito as $key => $value) {
                                   if(trim($financiera->usoCredito) == trim($value)){
                                        $estatus_solicitud ='negado'; 
                                        // $evaluacion_filtro = "negado";
                                        // $evaluacion_caso1 = "negado";
                                        break;
                                    }  
                                }
                                
                            }
                            if(!empty($caso17->valor) && $caso17->valor != ""){
                                $explodecomoTePagan = explode("|", $caso17->valor);
                                foreach ($explodecomoTePagan as $key => $value) {
                                   if(trim($financiera->comoTePagan) == trim($value)){
                                        $estatus_solicitud ='negado'; 
                                        // $evaluacion_filtro = "negado";
                                        // $evaluacion_caso17 = "negado";
                                        break;
                                    }  
                                }
                                
                            }
                        
    //    return "este es el status".$estatus_solicitud;

                        $solicitud->estatus = $estatus_solicitud;
                        $solicitud->puntaje_total = round($total_total,2);
                        $solicitud->save();
                        Log::error('Analisis cron=> '.$solicitud->id.':'.$estatus_solicitud);
                    
             
                }
            }
            }

            DB::commit(); // Guardamos la transaccion
       
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error inesperado.',
            ], 500);
        }
       

    }
}
