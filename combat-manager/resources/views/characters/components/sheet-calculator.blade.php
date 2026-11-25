@props(['characterTheme' => 'spellbound'])

{{-- V66.2.168 — calculadora compacta, teclado físico e expressões com dados via API. --}}
@once
    @push('styles')
        <style>
            .sb-sheet-calculator {
                position:fixed;left:var(--sb-calc-x,86px);top:var(--sb-calc-y,238px);
                z-index:319;width:min(328px,calc(100vw - 16px));max-height:calc(100dvh - 14px);
                display:flex;flex-direction:column;overflow:hidden;pointer-events:auto;
                border:1px solid var(--theme-modal-border,var(--theme-border));border-radius:17px;
                background:var(--theme-modal-surface,var(--theme-surface-card));
                color:var(--theme-text-primary);box-shadow:0 23px 55px rgba(0,0,0,.26),
                0 4px 14px rgba(0,0,0,.12),inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 25%,transparent);
                font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
            }
            .sb-sheet-calculator *{box-sizing:border-box}
            .sb-sheet-calculator__head{
                display:flex;align-items:center;gap:9px;padding:12px 13px;
                border-bottom:1px solid var(--theme-border-soft);cursor:grab;touch-action:none;user-select:none;
            }
            .sb-sheet-calculator.is-moving .sb-sheet-calculator__head{cursor:grabbing}
            .sb-sheet-calculator__emblem{flex:none;display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--theme-border-soft);border-radius:9px;background:var(--theme-surface-raised);color:var(--theme-accent-strong,var(--theme-accent))}
            .sb-sheet-calculator__head-copy{flex:1;min-width:0}
            .sb-sheet-calculator__kicker{display:block;color:var(--theme-label);font:800 7.5px/1.2 system-ui,sans-serif;letter-spacing:.15em;text-transform:uppercase}
            .sb-sheet-calculator__title{display:block;margin-top:2px;color:var(--theme-text-strong);font:900 17px/1.1 Georgia,serif}
            .sb-sheet-calculator__close{
                display:grid;place-items:center;width:28px;height:28px;border:1px solid var(--theme-border-soft);
                border-radius:8px;background:var(--theme-surface-raised);color:var(--theme-text-secondary);font-size:17px;cursor:pointer
            }
            .sb-sheet-calculator__close:hover{color:var(--theme-text-strong);background:var(--theme-card-hover)}
            .sb-sheet-calculator__screen{
                margin:11px 12px 10px;padding:13px 13px 12px;min-height:94px;flex:none;
                border:1px solid color-mix(in srgb,var(--theme-border) 73%,black 27%);border-radius:11px;
                background:linear-gradient(175deg,color-mix(in srgb,var(--theme-surface-raised) 90%,black 10%),var(--theme-surface-card));
                box-shadow:inset 0 3px 7px rgba(0,0,0,.12),inset 0 0 0 1px color-mix(in srgb,var(--theme-paper-strong) 20%,transparent);
                text-align:right;overflow:hidden;
            }
            .sb-sheet-calculator__formula{
                display:block;width:100%;min-height:22px;margin:0;padding:0;border:0;outline:0;
                color:var(--theme-text-secondary);background:transparent;text-align:right;
                font:700 13px/1.4 system-ui,sans-serif;font-variant-numeric:tabular-nums
            }
            .sb-sheet-calculator__formula:focus-visible{box-shadow:0 1px 0 var(--theme-accent)}
            .sb-sheet-calculator__die-detail{display:block;margin-top:4px;min-height:14px;
                font:650 9px/1.4 system-ui,sans-serif;color:var(--theme-text-secondary);overflow-wrap:anywhere} 
            .sb-sheet-calculator__result{
                display:block;width:100%;margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                color:var(--theme-text-strong);font:900 29px/1.12 Georgia,serif;font-variant-numeric:tabular-nums
            }
            .sb-sheet-calculator__board{
                position:relative;margin:0 12px 12px;padding:10px;
                border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 70%,black 30%);
                border-radius:13px;background:linear-gradient(160deg,color-mix(in srgb,var(--theme-surface-raised) 75%,black 25%),color-mix(in srgb,var(--theme-surface-card) 80%,black 20%));
                box-shadow:inset 0 3px 9px rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.045)
            }
            .sb-sheet-calculator__keys{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;padding-bottom:4px}
            .sb-sheet-calculator__key{
                position:relative;min-height:43px;padding:0 4px;display:flex;align-items:center;justify-content:center;
                border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 77%,black 23%);
                border-bottom-color:color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 62%,black 38%);
                border-radius:8px;background:linear-gradient(180deg,color-mix(in srgb,var(--theme-surface-card) 90%,white 10%),var(--theme-surface-raised));
                color:var(--theme-text-strong);font:850 15px/1 system-ui,sans-serif;font-variant-numeric:tabular-nums;
                box-shadow:0 3px 0 color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 72%,black 28%),inset 0 1px 0 rgba(255,255,255,.12);
                cursor:pointer;touch-action:manipulation;user-select:none;
                transition:transform .09s ease,background .13s ease,color .13s ease,box-shadow .09s ease
            }
            .sb-sheet-calculator__key:hover{background:var(--theme-card-hover);color:var(--theme-text-strong)}
            .sb-sheet-calculator__key:active{transform:translateY(3px);box-shadow:0 0 0 transparent,inset 0 1px 3px rgba(0,0,0,.14)}
            .sb-sheet-calculator__key:focus-visible,.sb-sheet-calculator__close:focus-visible{outline:2px solid var(--theme-input-focus,var(--theme-accent));outline-offset:2px}
            .sb-sheet-calculator__key.is-function{font-size:12px;font-weight:900;color:var(--theme-text-secondary)}
            .sb-sheet-calculator__key.is-dice{background:color-mix(in srgb,var(--theme-accent) 11%,var(--theme-surface-card));color:var(--theme-accent-strong,var(--theme-text-strong))}
            .sb-sheet-calculator__key.is-operator{
                background:linear-gradient(180deg,color-mix(in srgb,var(--theme-accent) 15%,var(--theme-surface-raised) 85%),color-mix(in srgb,var(--theme-accent) 10%,var(--theme-surface-card) 90%));
                color:var(--theme-accent-strong,var(--theme-text-strong))
            }
            .sb-sheet-calculator__key.is-operator:hover{background:color-mix(in srgb,var(--theme-accent) 21%,var(--theme-surface-card) 79%)}
            .sb-sheet-calculator__key.is-enter{
                border-color:var(--theme-accent-strong,var(--theme-accent));
                background:var(--theme-button-background,var(--theme-accent));color:var(--theme-button-text,#fff);
                box-shadow:0 3px 0 color-mix(in srgb,var(--theme-accent-strong,var(--theme-accent)) 75%,black 25%),inset 0 1px 0 rgba(255,255,255,.16)
            }
            .sb-sheet-calculator__key.is-enter:hover{filter:brightness(1.1)}
            @media(max-height:580px){.sb-sheet-calculator__board{overflow-y:auto}.sb-sheet-calculator__key{min-height:35px}.sb-sheet-calculator__screen{min-height:75px}}
            @media(prefers-reduced-motion:reduce){.sb-sheet-calculator__key{transition:none!important}}
        </style>
    @endpush
    <script>
        (() => {
            let registered=false;
            const register=()=>{
                if(registered||!window.Alpine)return;
                registered=true;
                window.Alpine.data('spellboundSheetCalculator', () => ({
                open:false,
                expression:'',
                calculated:'0',
                justEvaluated:false,
                running:false,
                error:'',
                diceDetail:'',
                position:{x:520,y:116},
                drag:null,
                init(){
                    try {
                        const saved=JSON.parse(localStorage.getItem('sb-sheet-calculator-pos-v167')||'null');
                        if(saved && Number.isFinite(saved.x) && Number.isFinite(saved.y)) this.position={x:saved.x,y:saved.y};
                    }catch(_error){}
                },
                toggle(){this.open ? this.close() : this.show();},
                show(){
                    this.open=true;
                    window.dispatchEvent(new CustomEvent('spellbound:calc-state',{detail:{open:true}}));
                    this.$nextTick(()=>{this.clamp();this.$refs?.formula?.focus();});
                },
                close(){
                    if(!this.open)return;
                    this.open=false;
                    this.stopDrag();
                    window.dispatchEvent(new CustomEvent('spellbound:calc-state',{detail:{open:false}}));
                },
                positionStyle(){return `--sb-calc-x:${Math.round(this.position.x)}px;--sb-calc-y:${Math.round(this.position.y)}px`;},
                clamp(){
                    const el=this.$refs?.window;
                    const w=el?.offsetWidth||Math.min(328,innerWidth-16);
                    const h=el?.offsetHeight||420;
                    this.position={
                        x:Math.max(7,Math.min(Math.max(7,innerWidth-w-7),Number(this.position.x)||7)),
                        y:Math.max(7,Math.min(Math.max(7,innerHeight-h-7),Number(this.position.y)||7)),
                    };
                },
                beginDrag(event){
                    if(event.pointerType==='mouse' && event.button!==0)return;
                    if(event.target.closest('button'))return;
                    this.drag={pointerId:event.pointerId,clientX:event.clientX,clientY:event.clientY,
                        x:this.position.x,y:this.position.y};
                },
                moveDrag(event){
                    if(!this.drag || event.pointerId!==this.drag.pointerId)return;
                    this.position={x:this.drag.x+event.clientX-this.drag.clientX,y:this.drag.y+event.clientY-this.drag.clientY};
                    this.clamp();
                },
                stopDrag(){
                    if(!this.drag)return;
                    this.drag=null;
                    try{localStorage.setItem('sb-sheet-calculator-pos-v167',JSON.stringify(this.position));}catch(_error){}
                },
                numberString(value){
                    if(!Number.isFinite(value))return 'Erro';
                    if(Math.abs(value)>1e13 || (Math.abs(value)<1e-7 && value!==0))return Number(value.toPrecision(9)).toString();
                    return new Intl.NumberFormat('pt-BR',{maximumFractionDigits:10,useGrouping:true}).format(Number(value.toPrecision(12)));
                },
                // Parser de aritmética simples, deliberadamente sem eval/Function.
                calculate(raw){
                    const text=String(raw||'').replace(/×/g,'*').replace(/÷/g,'/').replace(/\s/g,'');
                    if(!text || text.length>140)throw new Error('Expressão inválida');
                    const tokens=text.match(/(?:\d+(?:\.\d*)?|\.\d+)|[()+\-*/%]/g)||[];
                    if(tokens.join('')!==text)throw new Error('Expressão inválida');
                    let i=0;
                    function factor(){
                        const token=tokens[i];
                        if(token==='+'||token==='-'){i++;return (token==='-'?-1:1)*factor();}
                        let value;
                        if(token==='('){i++;value=expression();if(tokens[i]!==')')throw new Error('Parêntese incompleto');i++;}
                        else if(token!==undefined&&/^(?:\d+(?:\.\d*)?|\.\d+)$/.test(token)){value=Number(token);i++;}
                        else throw new Error('Expressão incompleta');
                        while(tokens[i]==='%'){value/=100;i++;}
                        return value;
                    }
                    function term(){
                        let value=factor();
                        while(tokens[i]==='*'||tokens[i]==='/'){
                            const op=tokens[i++],next=factor();
                            if(op==='/'&&next===0)throw new Error('Divisão por zero');
                            value=op==='*'?value*next:value/next;
                        }
                        return value;
                    }
                    function expression(){
                        let value=term();
                        while(tokens[i]==='+'||tokens[i]==='-'){
                            const op=tokens[i++],next=term();
                            value=op==='+'?value+next:value-next;
                        }
                        return value;
                    }
                    const result=expression();
                    if(i!==tokens.length||!Number.isFinite(result))throw new Error('Expressão inválida');
                    return Number(result.toPrecision(12));
                },
                preview(){
                    if(this.running)return 'Rolando…';
                    if(this.error)return 'Erro';
                    if(this.justEvaluated)return this.calculated;
                    if(!this.expression)return '0';
                    if(/[dD]/.test(this.expression))return 'Pronto para rolar';
                    try{return this.numberString(this.calculate(this.expression));}
                    catch(_error){return this.calculated;}
                },
                inputChanged(){
                    this.expression=String(this.expression||'').slice(0,140).replace(/[,]/g,'.').replace(/×/g,'*').replace(/÷/g,'/');
                    this.justEvaluated=false;
                    this.diceDetail='';
                    this.error='';
                },
                diceTokens(raw){
                    const text=String(raw||'').replace(/×/g,'*').replace(/÷/g,'/').replace(/,/g,'.').replace(/\s/g,'').toLowerCase();
                    if(!text||text.length>140)throw new Error('Expressão inválida.');
                    const tokens=text.match(/(?:\d*d\d+)|(?:\d+(?:\.\d*)?|\.\d+)|[()+\-*/%]/g)||[];
                    if(tokens.join('')!==text)throw new Error('Expressão inválida.');
                    return tokens;
                },
                async run(){
                    if(this.running)return;
                    this.error='';
                    this.diceDetail='';
                    const raw=String(this.expression||'').trim();
                    let tokens;
                    try {tokens=this.diceTokens(raw);}catch(error){this.error=error.message;return;}
                    const dice=tokens.map((token,index)=>({token,index,match:token.match(/^(\d*)d(\d+)$/)})).filter(entry=>entry.match);
                    const physicalTotal=dice.reduce((sum,entry)=>sum+Number(entry.match[1]||1),0);
                    if(physicalTotal>20){this.error='Máximo de 20 dados por cálculo.';return;}
                    if(dice.some(entry=>!Number.isInteger(Number(entry.match[1]||1)) || Number(entry.match[1]||1)<1 ||
                        Number(entry.match[1]||1)>20 || ![4,6,8,10,12,20,100].includes(Number(entry.match[2])))){
                        this.error='Use até 20 dados d4, d6, d8, d10, d12, d20 ou d100.';return;
                    }
                    this.running=true;
                    try {
                        const diceResults=await Promise.all(dice.map(async entry=>{
                            const expression=(entry.match[1]||'1')+'d'+entry.match[2];
                            const response=await fetch(@js(url('/api/roll')),{method:'POST',
                                headers:{'Content-Type':'application/json','Accept':'application/json',
                                    'X-CSRF-TOKEN':@js(csrf_token()),'X-Requested-With':'XMLHttpRequest'},
                                body:JSON.stringify({expression})});
                            const payload=await response.json().catch(()=>({}));
                            if(!response.ok)throw new Error(payload?.message||'Erro na API de dados.');
                            const roll=payload?.data??payload;
                            const value=Number(roll?.total??payload?.total);
                            const faces=Array.isArray(roll?.rolls)?roll.rolls.map(Number):[];
                            if(!Number.isFinite(value)||faces.length!==Number(entry.match[1]||1)||
                                faces.some(v=>!Number.isInteger(v)||v<1||v>Number(entry.match[2]))) {
                                throw new Error('Resultado de dados inválido.');
                            }
                            return {index:entry.index,token:entry.token,total:value,faces};
                        }));
                        const byIndex=new Map(diceResults.map(item=>[item.index,item]));
                        const numeric=tokens.map((token,index)=>byIndex.has(index)?String(byIndex.get(index).total):token).join('');
                        const answer=this.calculate(numeric);
                        this.calculated=this.numberString(answer);
                        this.diceDetail=diceResults.map(item=>`${item.token}: ${item.faces.join(' + ')} = ${item.total}`).join(' · ');
                        this.justEvaluated=true;
                    }catch(error){this.error=error?.message||'Não foi possível calcular.';}
                    finally{this.running=false;}
                },
                press(key){
                    if(this.running)return;
                    key=String(key);
                    if(key==='C'){this.expression='';this.calculated='0';this.justEvaluated=false;this.diceDetail='';this.error='';return;}
                    if(key==='⌫'){this.expression=this.expression.slice(0,-1);this.inputChanged();return;}
                    if(key==='='){void this.run();return;}
                    if(key==='×')key='*';
                    if(key==='÷')key='/';
                    if(this.justEvaluated && /^[0-9.d]$/.test(key))this.expression='';
                    this.justEvaluated=false;
                    this.error='';
                    this.diceDetail='';
                    if(this.expression.length>=140)return;
                    if(key==='.'){
                        const segment=this.expression.split(/[+\-*/()%d]/).pop()||'';
                        if(segment.includes('.'))return;
                        if(!segment)this.expression+='0';
                    }
                    if(key==='d' && /d$/i.test(this.expression))return;
                    if(/[+*/]/.test(key)&&/[+\-*/]$/.test(this.expression))this.expression=this.expression.slice(0,-1);
                    this.expression+=key;
                    if(!/[dD]/.test(this.expression)){
                        try{this.calculated=this.numberString(this.calculate(this.expression));}catch(_error){}
                    }
                },
                onKeyboard(event){
                    if(!this.open||event.ctrlKey||event.metaKey||event.altKey)return;
                    const el=event.target;
                    if(el===this.$refs?.formula)return;
                    if(el?.isContentEditable||['INPUT','TEXTAREA','SELECT'].includes(el?.tagName))return;
                    let key=event.key;
                    if(key==='Enter')key='=';
                    else if(key==='Backspace')key='⌫';
                    else if(key==='Escape'){this.close();return;}
                    else if(key===',')key='.';
                    if(!/^[0-9dD()+\-*/%.=]$/.test(key)&&key!=='⌫')return;
                    event.preventDefault();
                    this.press(key.toLowerCase());
                },
                }));
            };
            document.addEventListener('alpine:init',register,{once:true});
            if(window.Alpine)register();
        })();
    </script>
@endonce

<div x-data="spellboundSheetCalculator()" x-cloak
     @spellbound:toggle-calculator.window="toggle()"
     @spellbound:close-calculator.window="close()"
     @pointermove.window="moveDrag($event)"
     @pointerup.window="stopDrag()"
     @pointercancel.window="stopDrag()"
     @resize.window="clamp()"
     @keydown.window="onKeyboard($event)">
    <template x-teleport="body">
        <template x-if="open">
        <section x-ref="window" class="character-sheet-theme sb-sheet-calculator"
                 data-theme="{{ $characterTheme }}" :class="{'is-moving':!!drag}" :style="positionStyle()"
                 role="dialog" aria-modal="false" aria-label="Calculadora da ficha">
            <header class="sb-sheet-calculator__head" @pointerdown="beginDrag($event)">
                <span class="sb-sheet-calculator__emblem" aria-hidden="true">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4.5" y="2.5" width="15" height="19" rx="2"/><path d="M8 7h8M8 11h2m4 0h2M8 15h2m4 0h2M8 19h2m4 0h2"/></svg>
                </span>
                <span class="sb-sheet-calculator__head-copy">
                    <small class="sb-sheet-calculator__kicker">Cálculos · Dados</small>
                    <strong class="sb-sheet-calculator__title">Calculadora</strong>
                </span>
                <button type="button" class="sb-sheet-calculator__close" x-on:click.stop="close()" title="Fechar calculadora" aria-label="Fechar calculadora">×</button>
            </header>
            <div class="sb-sheet-calculator__screen" aria-label="Visor">
                <input x-ref="formula" type="text" class="sb-sheet-calculator__formula"
                       x-model="expression" x-on:input="inputChanged()"
                       x-on:keydown.enter.prevent="run()" x-on:keydown.escape.prevent="close()"
                       spellcheck="false" autocomplete="off"
                       aria-label="Expressão matemática ou de dados">
                <output class="sb-sheet-calculator__result" x-text="preview()" aria-live="polite"></output>
                <small class="sb-sheet-calculator__die-detail" x-show="diceDetail||error" x-text="error||diceDetail" aria-live="polite"></small>
            </div>
            <div class="sb-sheet-calculator__board">
                <div class="sb-sheet-calculator__keys" role="group" aria-label="Teclas de cálculo">
                    @foreach([
                        ['C','⌫','d','÷'],
                        ['7','8','9','×'],
                        ['4','5','6','−'],
                        ['1','2','3','+'],
                        ['%','0','.','=']
                    ] as $row)
                        @foreach($row as $key)
                            @if($key !== '')
                                <button type="button"
                                        class="sb-sheet-calculator__key {{ in_array($key,['C','⌫','%'],true) ? 'is-function' : '' }} {{ in_array($key,['÷','×','−','+'],true) ? 'is-operator' : '' }} {{ $key==='=' ? 'is-enter' : '' }} {{ $key==='d' ? 'is-dice' : '' }}"
                                        @click="press('{{ $key === '−' ? '-' : $key }}')"
                                        aria-label="{{ $key==='⌫' ? 'Apagar último dígito' : ($key==='d' ? 'Adicionar dado' : ($key==='C' ? 'Limpar' : $key)) }}">{{ $key }}</button>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </section>
        </template>
    </template>
</div>
