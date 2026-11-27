{{-- V66.2.259: preserva o palco completo da V66.2.255; modelos de dado dinâmicos para Vida e Atributos. --}}
@once
    @push('styles')
        <style>
            .sb-forge-stage-field{position:fixed;inset:0;z-index:335;pointer-events:none;overflow:hidden;isolation:isolate}
            .sb-forge-stage-die{position:absolute;left:50%;top:44%;width:74px;height:76px;margin:-38px 0 0 -37px;display:grid;place-items:center;
                pointer-events:none;will-change:transform,opacity;filter:drop-shadow(0 15px 12px rgba(24,16,15,.25))}
            .sb-forge-stage-die .sb-poly-die,.sb-forge-stage-die .sb-poly-number{pointer-events:none;backface-visibility:hidden}
            .sb-forge-stage-die.is-revealed .sb-poly-die{filter:drop-shadow(0 4px 2px rgba(18,11,8,.25))}
            @media(prefers-reduced-motion:reduce){.sb-forge-stage-die{will-change:auto}}
        </style>
    @endpush
    <script>
        (()=>{
            let installed=false;
            const register=()=>{
                if(installed||!window.Alpine)return;
                installed=true;
                window.Alpine.data('spellboundForgeDiceStage',()=>({
                    active:false, token:null, counter:0, dice:[], running:false,
                    manualRollOrigins:[],manualRollOrigin:null,rollMode:'free',rollPhysicsDuration:2340,
                    motionPromise:null,stopMotion:null,physicsAnimations:[],lastError:'',
                    init(){
                        const self=this;
                        window.SpellboundDiceFX={
                            owner:self,
                            start(request){return self.beginForgeFlight(request)},
                            finish(token,result){return self.finishForgeFlight(token,result)},
                            fail(token,message){self.failForgeFlight(token,message)},
                            cancel(token){self.cancelForgeFlight(token)},
                            hideLandedFreeDice(){self.active=false},
                        };
                    },
                    destroy(){
                        if(window.SpellboundDiceFX?.owner===this)window.SpellboundDiceFX=null;
                        this.cancelForgeFlight(this.token);
                    },
                    rollPhysicsRandom(min,max){return min+Math.random()*(max-min)},
                    rollPhysicsVisibleEntries(){return this.dice.map((die,index)=>({die,index}))},
                    prepareDiceMotionPlans() {
                        // Posiciona os grupos maiores primeiro para não prensar o d100
                        // quando há vários dados de dano numa tela estreita.
                        const entries = [...this.rollPhysicsVisibleEntries()].sort((a,b)=>
                            (Number(b.die.sides) === 100 ? 1 : 0) - (Number(a.die.sides) === 100 ? 1 : 0));
                        const w = Math.max(300, Number(window.innerWidth) || 1024);
                        const h = Math.max(440, Number(window.innerHeight) || 768);
                        const mobile = w < 600;
                        const fieldY = (mobile ? .41 : .44) * h;
                        const rand = (a, b) => this.rollPhysicsRandom(a, b);
                        const maxX = Math.max(72, Math.min(w * .43, w / 2 - 73));
                        const maxTop = Math.max(85, Math.min(280, fieldY - 82));
                        const maxBottom = Math.max(54, Math.min(180, h - fieldY - (mobile ? 128 : 150)));
                        const anchors = [
                            [-.95,-.72],[-.68,-.30],[-.36,-.86],[.07,-.85],[.48,-.74],[.91,-.63],
                            [-.93,.16],[-.58,.48],[-.16,.84],[.29,.78],[.69,.48],[.96,.08],
                            [-.53,-.91],[.75,-.26],[-.17,.10],[.37,-.20],[-.89,.76],[.88,.82],
                        ];
                        // Em cada rolagem, embaralha as regiões. A posição nunca é função do índice.
                        for (let j = anchors.length - 1; j > 0; j--) {
                            const k = Math.floor(Math.random() * (j + 1));
                            [anchors[j], anchors[k]] = [anchors[k], anchors[j]];
                        }
                        const origins = ['left-upper','left-center','left-lower',
                            'right-upper','right-center','right-lower',
                            'top-left','top-center','top-right',
                            'bottom-left','bottom-center','bottom-right'];
                        for (let j=origins.length-1; j>0; j--) {
                            const k=Math.floor(Math.random()*(j+1));
                            [origins[j],origins[k]]=[origins[k],origins[j]];
                        }
                        const positions = [];
                        const dense = entries.length >= 6;
                        // Em celulares, muitos dados livres são distribuídos em
                        // posições embaralhadas de uma malha espaçada. A malha é
                        // apenas para colisões finais; os voos continuam aleatórios.
                        const compactSpots = [];
                        if (mobile && entries.length >= 9 && this.rollMode === 'free') {
                            const rows = Math.max(4,Math.ceil(entries.length/3));
                            const edgeX=Math.min(maxX-3,Math.max(80,maxX*.85));
                            for(let row=0;row<rows;row++){
                                for(let col=-1;col<=1;col++){
                                    compactSpots.push({
                                        x:col*edgeX+rand(-1,1),
                                        y:-maxTop+4+(maxTop+maxBottom-8)*(row/Math.max(1,rows-1))+rand(-1,1),
                                    });
                                }
                            }
                            for(let i=compactSpots.length-1;i>0;i--){
                                const j=Math.floor(Math.random()*(i+1));
                                [compactSpots[i],compactSpots[j]]=[compactSpots[j],compactSpots[i]];
                            }
                        }
                        let maxDuration = 0;
                        entries.forEach((entry, order) => {
                            const die = entry.die;
                            const radius = Number(die.sides) === 100 ? (mobile ? 69 : 70) : (mobile ? 29 : 34);
                            let landing = compactSpots[order] || null;
                            const fits = candidate => positions.every(point => {
                                const dx = candidate.x - point.x;
                                const dy = candidate.y - point.y;
                                const minGap = radius + point.radius + (dense ? 5 : 15);
                                return Math.hypot(dx,dy) >= minGap;
                            });
                            for (let attempt=0; !landing && attempt<170; attempt++) {
                                const anchor=anchors[(order + attempt) % anchors.length];
                                const x=Math.round(Math.max(-maxX,Math.min(maxX,
                                    anchor[0]*maxX + rand(-24,24))));
                                const y=Math.round(anchor[1] < 0
                                    ? anchor[1]*maxTop + rand(-11,11)
                                    : anchor[1]*maxBottom + rand(-11,11));
                                if (fits({x,y})) { landing={x,y}; break; }
                            }
                            if (!landing) {
                                // Reposição por amostragem uniforme: não herda o alinhamento
                                // da grade antiga quando muitos dados são lançados juntos.
                                for (let attempt=0; attempt<600; attempt++) {
                                    const candidate={x:Math.round(rand(-maxX,maxX)),
                                        y:Math.round(rand(-maxTop,maxBottom))};
                                    if (fits(candidate)) {landing=candidate;break;}
                                }
                            }
                            if (!landing) {
                                // Busca por uma grade densa como último recurso em telas estreitas.
                                const columns = mobile ? 3 : 5;
                                const gapX = radius * 2 + 7;
                                const gapY = mobile ? 75 : 88;
                                const candidate={
                                    x:Math.round((order % columns - (columns-1)/2)*gapX),
                                    y:Math.round((Math.floor(order/columns) - .8)*gapY),
                                };
                                landing={x:Math.max(-maxX,Math.min(maxX,candidate.x)),
                                    y:Math.max(-maxTop,Math.min(maxBottom,candidate.y))};
                            }
                            positions.push({x:landing.x,y:landing.y,radius});
                            const origin=origins[order % origins.length];
                            const sidePadding=rand(80,200);
                            let fromX=rand(-w*.37,w*.37), fromY=-fieldY-sidePadding;
                            if (origin.startsWith('left')) {
                                fromX=-w/2-sidePadding;
                                fromY=origin.endsWith('upper') ? -fieldY*.68 :
                                    origin.endsWith('lower') ? (h-fieldY)*.54 : rand(-fieldY*.22,(h-fieldY)*.3);
                            } else if (origin.startsWith('right')) {
                                fromX=w/2+sidePadding;
                                fromY=origin.endsWith('upper') ? -fieldY*.68 :
                                    origin.endsWith('lower') ? (h-fieldY)*.54 : rand(-fieldY*.22,(h-fieldY)*.3);
                            } else if (origin.startsWith('top')) {
                                fromY=-fieldY-sidePadding;
                                fromX=origin.endsWith('left') ? -w*.36 :
                                    origin.endsWith('right') ? w*.36 : rand(-w*.15,w*.15);
                            } else {
                                fromY=h-fieldY+sidePadding;
                                fromX=origin.endsWith('left') ? -w*.36 :
                                    origin.endsWith('right') ? w*.36 : rand(-w*.15,w*.15);
                            }
                            const travelDirection = Math.random() < .5 ? -1 : 1;
                            const impactX=landing.x-travelDirection*rand(30,75);
                            const impactY=landing.y;
                            const duration=Math.round(rand(Number(die.sides)===20 ? 2040 : 1900,
                                Number(die.sides)===20 ? 2520 : 2460));
                            const delay=Math.round(rand(0,Math.min(230,entries.length*28+60)));
                            const impactAt=rand(.50,.59);
                            const firstBounce=rand(24,49);
                            const secondBounce=firstBounce*rand(.25,.42);
                            const thirdBounce=secondBounce*rand(.15,.31);
                            const dir=travelDirection;
                            const finalAngle=dir*720;
                            const impactAngle=finalAngle-dir*rand(205,330);
                            const startAngle=impactAngle-dir*rand(620,1050);
                            const exitAngle=rand(0,Math.PI*2);
                            const exitRadius=Math.max(w,h)*rand(.68,1.06);
                            die.motion={
                                x:landing.x,y:landing.y,fromX,fromY,
                                impactX,impactY,impactAt,duration,delay,
                                firstBounce,secondBounce,thirdBounce,
                                startAngle,impactAngle,finalAngle,
                                bend:rand(-75,75),scale:rand(.94,1.03),
                                exitX:landing.x+Math.cos(exitAngle)*exitRadius,
                                exitY:landing.y+Math.sin(exitAngle)*exitRadius,
                                exitRotation:dir*rand(320,660),
                                // Estilo estático antes de o navegador iniciar o voo.
                                style:`--sb-physics-x:${landing.x}px;--sb-physics-y:${landing.y}px;`,
                            };
                            const freeOrigin = (this.rollMode === 'free' || this.rollMode === 'death')
                                ? (this.manualRollOrigins?.[entry.index] || (order === 0 ? this.manualRollOrigin : null))
                                : null;
                            if (freeOrigin) {
                                // Cada dado livre preserva seu ponto real de soltura.
                                // Rolagens agrupadas partem simultaneamente de origens independentes.
                                const origin = freeOrigin;
                                const vx = Math.max(-4.2, Math.min(4.2, Number(origin.vx) || 0));
                                const vy = Math.max(-4.2, Math.min(4.2, Number(origin.vy) || 0));
                                const speed = Math.min(1.7, Math.hypot(vx, vy) / 1.75);
                                const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
                                const m = die.motion;
                                m.fromTray = true;
                                m.fromScale = clamp(Number(origin.scale)||1,.85,1.12);
                                // A origem manual segue a MESMA regra para dados livres
                                // e salvamentos: o gesto define o lançamento, não o modo.
                                m.fromX = clamp(Number(origin.x) - w / 2, -w/2, w/2);
                                m.fromY = clamp(Number(origin.y) - fieldY, -fieldY, h-fieldY);
                                m.startAngle = clamp(Number(origin.tilt)||0,-180,180);
                                const forward = 135 + 82 * speed;
                                // O giro segue o sentido real do lançamento; não sorteia
                                // arbitrariamente uma rotação contrária ao arraste forte.
                                const spinDir = Math.abs(vx) > .16 ? Math.sign(vx) : dir;
                                // O pouso é definido ANTES do giro: converge naturalmente
                                // para uma volta completa, igual ao dado interativo na mesa.
                                // Isso elimina a virada brusca ao passar de palco para bandeja.
                                const rotations = Math.max(2,Math.min(5,Math.round(2.15+speed*.95+rand(-.28,.28))));
                                m.finalAngle = Math.round(m.startAngle / 360) * 360 + spinDir * 360 * rotations;
                                m.impactAngle = m.finalAngle - spinDir * rand(190,290) * (1 + .13 * speed);
                                // Arremesso manual: espalhamento proporcional à energia.
                                // Um gesto leve não recebe o mesmo impacto de um lançamento forte.
                                m.impactX = clamp(m.fromX + vx * forward + rand(-19,19)*(1+.38*speed), -maxX, maxX);
                                m.impactY = clamp(m.fromY + vy * (94 + speed * 36) + rand(-12,12)*(1+.36*speed), -maxTop, maxBottom);
                                // Momento do arraste propõe a zona de pouso. O resolvedor
                                // distribui as peças para que não terminem empilhadas,
                                // inclusive as duas metades físicas de um d100.
                                const desiredX=clamp(m.impactX + Math.sign(vx || (Math.random()-.5))
                                    * rand(30,78) * (1+.58*speed), -maxX, maxX);
                                const desiredY=clamp(m.impactY + rand(-9,24) + Math.sign(vy)*speed*9,
                                    -maxTop,maxBottom);
                                const others=positions.slice(0,-1);
                                const clearLanding=(point)=>others.every(other=>
                                    Math.hypot(point.x-other.x,point.y-other.y) >=
                                        radius+other.radius+(dense?5:12));
                                let finalPoint=compactSpots.length ? landing : {x:desiredX,y:desiredY};
                                if(!clearLanding(finalPoint)){
                                    finalPoint=null;
                                    // Procura o ponto livre mais próximo do impulso;
                                    // o ângulo áureo evita que os dados formem uma grade.
                                    const phase=rand(0,Math.PI*2);
                                    for(let trial=0;trial<640;trial++){
                                        const ring=Math.sqrt(trial+1)*14.6;
                                        const angle=phase+trial*2.39996323;
                                        const candidate={
                                            x:clamp(desiredX+Math.cos(angle)*ring,-maxX,maxX),
                                            y:clamp(desiredY+Math.sin(angle)*ring,-maxTop,maxBottom),
                                        };
                                        if(clearLanding(candidate)){finalPoint=candidate;break;}
                                    }
                                }
                                if(!finalPoint){
                                    // Dispersão uniforme: impede que colisões sucessivas
                                    // prendam todas as peças à mesma borda da tela.
                                    for(let trial=0;trial<800;trial++){
                                        const candidate={x:rand(-maxX,maxX),y:rand(-maxTop,maxBottom)};
                                        if(clearLanding(candidate)){finalPoint=candidate;break;}
                                    }
                                }
                                if(!finalPoint)finalPoint=landing;
                                m.x=finalPoint.x;
                                m.y=finalPoint.y;
                                positions[positions.length-1]={x:m.x,y:m.y,radius};
                                m.bend = rand(-27,27) + vx * (20 + speed * 8);
                                m.impactAt = rand(.47,.55) - Math.min(.055, speed*.03);
                                m.firstBounce = rand(16,28) + speed * 25;
                                m.secondBounce = m.firstBounce * rand(.29,.41);
                                m.thirdBounce = m.secondBounce * rand(.15,.28);
                                m.duration = Math.round(rand(2020,2320) + speed * 290);
                                if (speed > .48) {
                                    // Saída coerente com o impulso, com leve desvio.
                                    const exitAngle = Math.atan2(vy,vx) + rand(-.48,.48);
                                    const exitDistance = Math.max(w,h)*rand(.66,.98);
                                    m.exitX = m.x + Math.cos(exitAngle)*exitDistance;
                                    m.exitY = m.y + Math.sin(exitAngle)*exitDistance;
                                    m.exitRotation = spinDir*rand(350,690);
                                }
                                // No dado manual, começa no mesmo frame da soltura.
                                m.delay = 0;
                                // Colisão opcional com a borda real da janela. A trajetória
                                // reflete ao encontrar a parede e continua até o primeiro impacto.
                                // Afeta somente os dados livres, nunca os ataques já aprovados.
                                const collisionX = w / 2 - radius - 7;
                                const collisionY = h - fieldY - radius - 14;
                                const ceilingY = -fieldY + radius + 8;
                                const travelX = m.fromX + vx * rand(150,250);
                                const travelY = m.fromY + vy * rand(135,205);
                                if (travelX > collisionX || travelX < -collisionX) {
                                    m.wallCollision={axis:'x',value:travelX > 0 ? collisionX : -collisionX,
                                        at:rand(.27,.43)};
                                } else if (travelY > collisionY || travelY < ceilingY) {
                                    m.wallCollision={axis:'y',value:travelY > 0 ? collisionY : ceilingY,
                                        at:rand(.29,.45)};
                                } else if (Math.abs(m.fromX) > collisionX * .79 && Math.abs(vx) > .34 && Math.sign(vx) === Math.sign(m.fromX)) {
                                    m.wallCollision={axis:'x',value:Math.sign(vx)*collisionX,
                                        at:rand(.26,.39)};
                                }
                                maxDuration = Math.max(maxDuration, m.duration+m.delay);
                            }
                            maxDuration=Math.max(maxDuration,duration+delay);
                        });
                        this.rollPhysicsDuration=Math.ceil(maxDuration+100);
                    },

                    rollPhysicsFrames(motion) {
                        const lerp=(a,b,t)=>a+(b-a)*t;
                        const easeOut=t=>1-Math.pow(1-t,2.65);
                        const frames=[];
                        // 96 quadros-alvo: renderização intermediária feita no compositor,
                        // sem setInterval, sem blocos de animação com novas origens.
                        for (let i=0; i<=96; i++) {
                            const t=i/96;
                            let x,y,angle,scale;
                            if (t <= motion.impactAt) {
                                const u=Math.min(1,t/motion.impactAt);
                                const wall=motion.wallCollision;
                                const hasWall=wall && wall.at>0 && wall.at<1;
                                if(hasWall && wall.axis==='x') {
                                    // A inversão de velocidade e contínua na posição da parede.
                                    x=u<=wall.at ? lerp(motion.fromX,wall.value,u/wall.at)
                                        : lerp(wall.value,motion.impactX,(u-wall.at)/(1-wall.at));
                                } else {
                                    x=lerp(motion.fromX,motion.impactX,u)+motion.bend*Math.sin(Math.PI*u);
                                }
                                const height=Math.max(45,
                                    Math.abs(motion.fromX-motion.impactX)*.075,
                                    Math.max(0,motion.fromY-motion.impactY)*.43);
                                if(hasWall && wall.axis==='y') {
                                    const after=Math.max(0,(u-wall.at)/(1-wall.at));
                                    y=u<=wall.at ? lerp(motion.fromY,wall.value,u/wall.at)
                                        : lerp(wall.value,motion.impactY,after)-height*.27*Math.sin(Math.PI*after);
                                } else {
                                    y=lerp(motion.fromY,motion.impactY,u)
                                        - height*Math.sin(Math.PI*u)*(0.25+.75*u);
                                }
                                angle=lerp(motion.startAngle,motion.impactAngle,Math.pow(u,.90));
                                scale=lerp(motion.fromTray ? (motion.fromScale||1) : .83,motion.scale,u);
                                if(motion.fromTray && hasWall) {
                                    // Compressão e contragiro curtos no contato com a parede.
                                    // Envelope senoidal com derivada zero nas extremidades:
                                    // o dado não salta de orientação ao ricochetear.
                                    const relative=(u-(wall.at-.115))/.23;
                                    if(relative>0 && relative<1) {
                                        const recoil=Math.pow(Math.sin(Math.PI*relative),2);
                                        const spinSign=Math.sign(motion.impactAngle-motion.startAngle)||1;
                                        angle+=spinSign*(wall.axis==='x'?-1:1)*8.5*recoil;
                                        scale-=.031*recoil;
                                    }
                                }
                            } else {
                                const finishAt=.955;
                                const v=Math.min(1,(t-motion.impactAt)/(finishAt-motion.impactAt));
                                // Três arcos reais de quique: amplitude e duração decrescentes.
                                const b1=.45, b2=.77, b3=.96;
                                let bounce=0;
                                if (v<b1) bounce=motion.firstBounce*Math.sin(Math.PI*v/b1);
                                else if (v<b2) bounce=motion.secondBounce*Math.sin(Math.PI*(v-b1)/(b2-b1));
                                else if (v<b3) bounce=motion.thirdBounce*Math.sin(Math.PI*(v-b2)/(b3-b2));
                                // Dado lançado à mão: conserva mais energia horizontal
                                // imediatamente após o impacto e perde velocidade aos poucos.
                                const travelProgress = motion.fromTray
                                    ? 1-Math.pow(1-v,1.82) : easeOut(v);
                                x=lerp(motion.impactX,motion.x,travelProgress);
                                // Continua exatamente do ponto de impacto, inclusive quando
                                // a soltura manual produz rolamento vertical adicional.
                                y=lerp(motion.impactY,motion.y,easeOut(v))-bounce;
                                angle=lerp(motion.impactAngle,motion.finalAngle,easeOut(v));
                                // Evita o pequeno salto de escala na transição voo/quique.
                                scale=lerp(motion.scale,1,Math.min(1,v/.26))
                                    +(.018*(1-v))*Math.abs(Math.sin(v*Math.PI*3));
                            }
                            frames.push({
                                offset:t,
                                transform:`translate3d(${x.toFixed(2)}px,${y.toFixed(2)}px,0) rotate(${angle.toFixed(2)}deg) scale(${scale.toFixed(3)})`,
                                opacity:motion.fromTray ? 1 : (t<.055 ? Math.max(0,t/.055) : 1),
                            });
                        }
                        return frames;
                    },

                    // Compensa o giro do poliedro: a face pode girar, mas a
                    // tipografia permanece sempre em pé durante o lançamento.
                    rollUprightNumberFrames(frames) {
                        return frames.map(frame => {
                            const angle = /rotate\((-?[\d.]+)deg\)/.exec(frame.transform);
                            return {offset:frame.offset, rotate:`${-(Number(angle?.[1]) || 0)}deg`};
                        });
                    },


                    cancelPhysics(){
                        (this.physicsAnimations||[]).forEach(animation=>{try{animation.cancel()}catch(_err){}});
                        this.physicsAnimations=[];
                    },
                    beginForgeFlight(request={}){
                        if(this.running)return null;
                        const parts=Array.isArray(request.parts)?request.parts:[];
                        if(!parts.length||parts.length>30)return null;
                        this.cancelPhysics();
                        this.active=false;
                        this.manualRollOrigins=Array.isArray(request.origins)?request.origins:[];
                        this.manualRollOrigin=this.manualRollOrigins[0]||null;
                        this.dice=parts.map((item,index)=>{
                            const parsed=String(item?.expression||'1d6').match(/d(6|8|10|12)(?!\d)/i);
                            const sides=parsed ? Number(parsed[1]) : 6;
                            return {id:index,sides,display:String(item?.initialFace??'?'),face:null,revealed:false,motion:null};
                        });
                        this.rollMode='free';
                        // A distribuição e os movimentos são os mesmos do método original de Ataques.
                        this.prepareDiceMotionPlans();
                        const token=this.token='forge-'+(++this.counter);
                        this.running=true;
                        this.active=true;
                        let done;
                        this.motionPromise=new Promise(resolve=>{done=resolve});
                        this.stopMotion=done;
                        this.$nextTick(()=>window.requestAnimationFrame(()=>{
                            if(this.token!==token)return;
                            const nodes=[...(this.$refs.forgeField?.querySelectorAll('[data-forge-motion-index]')||[])];
                            const reduced=window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
                            const animations=[];
                            nodes.forEach(node=>{
                                const index=Number(node.dataset.forgeMotionIndex);
                                const motion=this.dice[index]?.motion;
                                if(!motion)return;
                                const finish=`translate3d(${motion.x}px,${motion.y}px,0)`;
                                if(reduced||!node.animate){node.style.transform=finish;node.style.opacity='1';return}
                                const keyframes=this.rollPhysicsFrames(motion);
                                const options={duration:motion.duration,delay:motion.delay,easing:'linear',fill:'forwards',iterations:1};
                                const animation=node.animate(keyframes,options);
                                this.physicsAnimations.push(animation);
                                animations.push(animation.finished.catch(()=>{}));
                                const counterFrames=this.rollUprightNumberFrames(keyframes);
                                node.querySelectorAll('.sb-poly-number').forEach(num=>{
                                    const counter=num.animate(counterFrames,options);
                                    this.physicsAnimations.push(counter);
                                });
                            });
                            try{request.onMotionStart?.()}catch(_error){}
                            Promise.all(animations).then(()=>done());
                        }));
                        return token;
                    },
                    async finishForgeFlight(token,result={}){
                        if(token!==this.token)return false;
                        await this.motionPromise;
                        if(token!==this.token)return false;
                        const faces=Array.isArray(result.rolls)?result.rolls:[];
                        this.dice.forEach((die,index)=>{
                            const face=Number(faces[index]);die.face=Number.isInteger(face)?face:null;
                            die.display=die.face===null?'?':String(die.face);die.revealed=true;
                        });
                        await new Promise(resolve=>window.setTimeout(resolve,140));
                        if(token!==this.token)return false;
                        const w=window.innerWidth,h=window.innerHeight;
                        const snapshot=this.dice.map(die=>({pieces:[{
                            x:w*.5+die.motion.x,y:h*.44+die.motion.y,rotation:0,face:die.face,
                        }]}));
                        this.running=false;
                        return {landings:snapshot};
                    },
                    failForgeFlight(token,message){
                        if(token!==this.token)return;
                        this.lastError=message||'Não foi possível rolar.';
                        this.cancelForgeFlight(token);
                    },
                    cancelForgeFlight(token){
                        if(token!==this.token)return;
                        this.cancelPhysics();
                        this.running=false;this.active=false;this.token=null;
                        this.stopMotion?.();this.stopMotion=null;
                    },
                }));
            };
            document.addEventListener('alpine:init',register,{once:true});
            if(window.Alpine)register();
        })();
    </script>
@endonce
<div x-data="spellboundForgeDiceStage()" x-cloak @spellbound:forge-close-dice.window="cancelForgeFlight(token)">
    <template x-teleport="body">
        <div x-show="active" x-cloak class="character-sheet-theme sb-forge-stage-field" data-theme="spellbound"
             x-ref="forgeField" role="status" aria-label="Rolagem de dados sobre a tela">
            <template x-for="die in dice" :key="die.id">
                <div class="sb-forge-stage-die" :data-forge-motion-index="die.id" :class="{'is-revealed':die.revealed}">
                    @foreach ([6, 8, 10, 12] as $sides)
                        <template x-if="die.sides === {{ $sides }}">
                            <x-ui.poly-die :sides="$sides" size="64"><span x-text="die.display">?</span></x-ui.poly-die>
                        </template>
                    @endforeach
                </div>
            </template>
        </div>
    </template>
</div>
