import { useAuthActions } from "@convex-dev/auth/react";
import { useState } from "react";
import { Input } from "@mui/material";
import { ConvexError } from "convex/values";
import { set } from "react-hook-form";

export function AuthForm() {
  const { signIn } = useAuthActions();
  const [step, setStep] = useState<"signUp" | "signIn">("signIn");
  const [error, setError]=useState('')


  if(step === "signUp"){
  return (
    <div className="items-center grid p-10">
    <form
    className="grid gap-10 "
      onSubmit={(event) => {
        event.preventDefault();
        const formData = new FormData(event.currentTarget);
        signIn("password", formData).catch((error)=>{
            console.log("Error signing up:", error);
            setError('Failed to sign up. Please try again.');
        });
      }}
    >
        <div>
            <h1 className="text-center text-xl">Sign up</h1>
            {error && <p className="text-red-500 mt-4 text-center">{error}</p>}
            <Input name="flow" type="hidden" value={step} />
        </div>
        
      <Input name="email" placeholder="Email" type="text" />
      <Input name="fullname" placeholder="Full Name" type="text" />
      <Input name="password" placeholder="Password" type="password" />
      
      <button  
      className='p-2 text-white cursor-pointer rounded-lg bg-amber-500 hover:bg-amber-600 w-1/2 justify-self-center transition-all duration-300' 
      type="submit">Sign up</button>
      
    </form>

    <button
    className="hover:underline hover:text-amber-500 mt-8"
        type="button"
        onClick={() => {
          setStep("signIn");
        }}
      >
      Already have an account? Sign im
      </button>
    </div>
  );
}else {
    return(
        <div className="items-center grid p-10" >
    <form
    className="grid gap-10 p-10 "
      onSubmit={(event) => {
      
        event.preventDefault();
        const formData = new FormData(event.currentTarget);

      
        signIn("password", formData).catch((error)=>{
            console.log("Error signing in:", error);
            setError('Failed to sign in. Please check your credentials and try again.');
        });
        
    
      }}
    >
        <div>
            <h1 className="text-center text-xl">Sign in to StrathMart to begin</h1>
            {error && <p className="text-red-500 mt-4 text-center">{error}</p>}
            <Input className="" name="flow" type="hidden" value={step} />
        </div>
      <Input name="email" placeholder="Email" type="text" />
      <Input name="password" placeholder="Password" type="password" />
      <button  
      className='p-2 text-white cursor-pointer rounded-lg bg-amber-500 hover:bg-amber-600 w-1/2 justify-self-center transition-all duration-300'
       type="submit">Sign in</button>
     
    </form>

     <button
        className="hover:underline hover:text-amber-500"
        type="button"
        onClick={() => {
          setStep("signUp");
        }}
      >
     Dont have an account? Sign up
      </button>
      </div>
    )

}
}